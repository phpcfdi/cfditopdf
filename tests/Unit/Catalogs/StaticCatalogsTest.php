<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit\Catalogs;

use PhpCfdi\CfdiToPdf\Catalogs\StaticCatalogs;
use PhpCfdi\CfdiToPdf\Tests\TestCase;

class StaticCatalogsTest extends TestCase
{
    public function testGetValueUsingValidData(): void
    {
        $catalogs = new StaticCatalogs();
        $this->assertSame('I - Ingreso', $catalogs->catTipoComprobante('I'));
        $this->assertSame('01 - Efectivo', $catalogs->catFormaPago('01'));
        $this->assertSame('PUE - Pago en una sola exhibición', $catalogs->catMetodoPago('PUE'));
        $this->assertSame('01 - No aplica', $catalogs->catExportacion('01'));
        $this->assertSame('601 - General de Ley Personas Morales', $catalogs->catRegimenFiscal('601'));
        $this->assertSame('G01 - Adquisición de mercancías', $catalogs->catUsoCFDI('G01'));
    }
}
