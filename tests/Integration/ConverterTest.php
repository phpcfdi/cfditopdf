<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Integration;

use CfdiUtils\Cfdi;
use CfdiUtils\Cleaner\Cleaner;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Converter;
use PhpCfdi\CfdiToPdf\Tests\PdfToText\PdfToText;
use PhpCfdi\CfdiToPdf\Tests\TestCase;

/**
 * @covers \PhpCfdi\CfdiToPdf\Converter
 */
class ConverterTest extends TestCase
{
    public function testConverter()
    {
        $cfdi = Cfdi::newFromString(Cleaner::staticClean($this->fileContents('cfdi33-valid.xml')));

        $cfdiData = (new CfdiDataBuilder())
            ->withXmlResolver($this->createXmlResolver())
            ->build($cfdi->getNode());
        $uuid = $cfdiData->timbreFiscalDigital()['UUID'];

        $builder = new Html2PdfBuilder();
        $converter = new Converter($builder);

        $created = $this->filePath('cfdi33-valid.pdf');
        $converter->createPdfAs($cfdiData, $created);
        $this->assertFileExists($created);

        $pdfToString = new PdfToText();
        $contents = $pdfToString->extract($created);
        $this->assertContains($uuid, $contents);

        unlink($created);
    }

    public function testConverterWithPaymentData()
    {
        $cfdi = Cfdi::newFromString(Cleaner::staticClean($this->fileContents('cfdi33-payment-valid.xml')));

        $cfdiData = (new CfdiDataBuilder())
            ->withXmlResolver($this->createXmlResolver())
            ->build($cfdi->getNode());

        $pagos = $cfdiData->comprobante()->searchNodes('cfdi:Complemento', 'pago10:Pagos', 'pago10:Pago');

        $pago = $pagos->first();
        if (null === $pago) {
            $this->fail('Specimen does not have a Pago element');
            return;
        }

        $doctosRelacionados = $pago->searchNodes('pago10:DoctoRelacionado');

        $doctoRelacionado = $doctosRelacionados->first();
        if (null === $doctoRelacionado) {
            $this->fail('Specimen does not have a DoctoRelacionado element');
            return;
        }

        $builder = new Html2PdfBuilder();
        $converter = new Converter($builder);

        $created = $this->filePath('cfdi33-payment-valid.pdf');
        $converter->createPdfAs($cfdiData, $created);
        $this->assertFileExists($created);

        $pdfToString = new PdfToText();
        $contents = $pdfToString->extract($created);
        $this->assertContains($doctoRelacionado['IdDocumento'], $contents);
        $this->assertContains($doctoRelacionado['Serie'], $contents);
        $this->assertContains($doctoRelacionado['Folio'], $contents);
        $this->assertContains($doctoRelacionado['MonedaDR'], $contents);
        $this->assertContains($doctoRelacionado['TipoCambioDR'], $contents);
        $this->assertContains($doctoRelacionado['MetodoDePagoDR'], $contents);
        $this->assertContains($doctoRelacionado['NumParcialidad'], $contents);
        $this->assertContains($doctoRelacionado['ImpPagado'], $contents);
        $this->assertContains($doctoRelacionado['ImpSaldoInsoluto'], $contents);
        $this->assertContains($doctoRelacionado['ImpSaldoAnt'], $contents);
        unlink($created);
    }
}
