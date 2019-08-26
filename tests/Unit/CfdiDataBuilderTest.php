<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit;

use CfdiUtils\CadenaOrigen\XsltBuilderInterface;
use CfdiUtils\Cfdi;
use CfdiUtils\Nodes\NodeInterface;
use CfdiUtils\XmlResolver\XmlResolver;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \PhpCfdi\CfdiToPdf\CfdiDataBuilder
 */
class CfdiDataBuilderTest extends TestCase
{
    public function testWithXmlResolver()
    {
        $firstBuilder = new CfdiDataBuilder();
        $defaultResolver = $firstBuilder->xmlResolver();
        /** @var XmlResolver&MockObject $secondResolver */
        $secondResolver = $this->createMock(XmlResolver::class);
        $secondBuilder = $firstBuilder->withXmlResolver($secondResolver);

        $this->assertSame($firstBuilder, $secondBuilder);
        $this->assertNotSame($defaultResolver, $secondResolver);
    }

    public function testWithXsltBuilder()
    {
        $firstBuilder = new CfdiDataBuilder();
        $defaultXsltBuilder = $firstBuilder->xsltBuilder();
        /** @var XsltBuilderInterface&MockObject $secondXsltBuilder */
        $secondXsltBuilder = $this->createMock(XsltBuilderInterface::class);
        $secondBuilder = $firstBuilder->withXsltBuilder($secondXsltBuilder);

        $this->assertSame($firstBuilder, $secondBuilder);
        $this->assertNotSame($defaultXsltBuilder, $secondXsltBuilder);
    }

    public function testBuild()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var CfdiDataBuilder&MockObject $builder */
        $builder = $this->getMockBuilder(CfdiDataBuilder::class)
            ->enableOriginalConstructor()
            ->setMethodsExcept(['build'])
            ->getMock();
        $builder->method('createQrUrl')->willReturn('qr');
        $builder->method('createTfdSourceString')->willReturn('tfd');

        $cfdiData = $builder->build($comprobante);

        $this->assertSame($comprobante, $cfdiData->comprobante());
        $this->assertSame('qr', $cfdiData->qrUrl());
        $this->assertSame('tfd', $cfdiData->tfdSourceString());
    }

    public function testCreateTfdSourceStringWithoutTimbreFiscalDigital()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var NodeInterface $complemento phpstan recognize null returned by searchNode */
        $complemento = $comprobante->searchNode('cfdi:Complemento');
        $complemento->children()->removeAll();

        $builder = new CfdiDataBuilder();
        $this->assertSame('', $builder->createTfdSourceString($comprobante));
    }

    public function testCreateTfdSourceStringWithTfd11()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        $builder = new CfdiDataBuilder();
        $this->assertStringContainsString(
            '|1.1|9FB6ED1A-5F37-4FEF-980A-7F8C83B51894|',
            $builder->createTfdSourceString($comprobante)
        );
    }

    public function testCreateTfdSourceStringWithTfd10()
    {
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        /** @var NodeInterface $complemento phpstan recognize null returned by searchNode */
        $complemento = $comprobante->searchNode('cfdi:Complemento');
        /** @var NodeInterface $tfd phpstan recognize null returned by firstNodeWithName */
        $tfd = $complemento->children()->firstNodeWithName('tfd:TimbreFiscalDigital');
        $tfd->addAttributes([
            'Version' => null,
            'version' => '1.0',
        ]);

        $builder = new CfdiDataBuilder();
        $this->assertStringContainsString(
            '|1.0|9FB6ED1A-5F37-4FEF-980A-7F8C83B51894|',
            $builder->createTfdSourceString($comprobante)
        );
    }

    public function testCreateQrUrl()
    {
        // this method is only an utility for CfdiUtils\ConsultaCfdiSat\RequestParameters::expression()
        $comprobante = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'))->getNode();
        $builder = new CfdiDataBuilder();
        $expectedQr = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
            . '?id=9FB6ED1A-5F37-4FEF-980A-7F8C83B51894&re=AAA010101AAA&rr=COSC8001137NA&tt=2382870.0&fe=osRJ2Q==';
        $this->assertSame($expectedQr, $builder->createQrUrl($comprobante));
    }
}
