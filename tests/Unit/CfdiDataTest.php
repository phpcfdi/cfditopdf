<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit;

use CfdiUtils\Cfdi;
use CfdiUtils\Nodes\NodeInterface;
use PhpCfdi\CfdiToPdf\CfdiData;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use RuntimeException;

/**
 * @covers \PhpCfdi\CfdiToPdf\CfdiData
 */
class CfdiDataTest extends TestCase
{
    public function testConstructUsingValidContent()
    {
        $cfdi = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'));
        $comprobante = $cfdi->getNode();
        $cfdiData = new CfdiData($comprobante, 'qr', 'tfd');
        $this->assertSame($comprobante, $cfdiData->comprobante());
        $this->assertSame('qr', $cfdiData->qrUrl());
        $this->assertSame('tfd', $cfdiData->tfdSourceString());
        $this->assertSame('AAA010101AAA', $cfdiData->emisor()['Rfc']);
        $this->assertSame('COSC8001137NA', $cfdiData->receptor()['Rfc']);
        $this->assertSame('9FB6ED1A-5F37-4FEF-980A-7F8C83B51894', $cfdiData->timbreFiscalDigital()['UUID']);
    }

    public function testConstructWithoutEmisor()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var NodeInterface<NodeInterface> $emisor phpstan recognize null returned by searchNode */
        $emisor = $comprobante->searchNode('cfdi:Emisor');
        $comprobante->children()->remove($emisor);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El CFDI no contiene nodo emisor');
        new CfdiData($comprobante, 'qr', 'tfd');
    }

    public function testConstructWithoutReceptor()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var NodeInterface<NodeInterface> $receptor phpstan recognize null returned by searchNode */
        $receptor = $comprobante->searchNode('cfdi:Receptor');
        $comprobante->children()->remove($receptor);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El CFDI no contiene nodo receptor');
        new CfdiData($comprobante, 'qr', 'tfd');
    }

    public function testConstructWithoutComplemento()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var NodeInterface<NodeInterface> $complemento phpstan recognize null returned by searchNode */
        $complemento = $comprobante->searchNode('cfdi:Complemento');
        $complemento->children()->removeAll();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El CFDI no contiene complemento de timbre fiscal digital');
        new CfdiData($comprobante, 'qr', 'tfd');
    }
}
