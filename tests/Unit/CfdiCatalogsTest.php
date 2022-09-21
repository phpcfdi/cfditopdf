<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit;

use CfdiUtils\Cfdi;
use PhpCfdi\CfdiToPdf\CfdiCatalogs;
use PhpCfdi\CfdiToPdf\CfdiData;
use PhpCfdi\CfdiToPdf\Tests\TestCase;

/**
 * @covers \PhpCfdi\CfdiToPdf\CfdiDataBuilder
 */
class CfdiCatalogsTest extends TestCase
{
    public function testGetValueUsingValidData(): void
    {
        $catalogs = new CfdiCatalogs();
        $cfdi = Cfdi::newFromString($this->fileContents('cfdi33-valid.xml'));
        $comprobante = $cfdi->getNode();
        $cfdiData = new CfdiData($comprobante, 'qr', 'tfd');
        $this->assertSame(
            'I - Ingreso',
            $catalogs->catTipoComprobante(strval($cfdiData->comprobante()['TipoDeComprobante'])),
        );
        $this->assertSame(
            '01 - Efectivo',
            $catalogs->catFormaPago(strval($cfdiData->comprobante()['FormaPago'])),
        );
        $this->assertSame(
            'PUE - Pago en una sola exhibición',
            $catalogs->catMetodoPago(strval($cfdiData->comprobante()['MetodoPago'])),
        );
        $this->assertSame(
            '',
            $catalogs->catExportacion(strval($cfdiData->comprobante()['Exportacion'])),
        );
        $this->assertSame(
            '601 - General de Ley Personas Morales',
            $catalogs->catRegimenFiscal(strval($cfdiData->emisor()['RegimenFiscal'])),
        );
        $this->assertSame(
            'G01 - Adquisición de mercancías',
            $catalogs->catUsoCFDI(strval($cfdiData->receptor()['UsoCFDI'])),
        );
    }
}