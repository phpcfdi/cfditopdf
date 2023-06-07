<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Integration;

use CfdiUtils\Cfdi;
use CfdiUtils\Cleaner\Cleaner;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Converter;
use PhpCfdi\CfdiToPdf\Internal\CastToStringTrait;
use PhpCfdi\CfdiToPdf\Tests\PdfToText\PdfToText;
use PhpCfdi\CfdiToPdf\Tests\TestCase;

/**
 * @covers \PhpCfdi\CfdiToPdf\Converter
 */
class ConverterTest extends TestCase
{
    use CastToStringTrait;

    /** @var PdfToText */
    private $pdfToText;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfToText = new PdfToText();
        if (! $this->pdfToText->exists()) {
            $this->markTestSkipped('pdftotext tool is not installed');
        }
    }

    public function testConverter(): void
    {
        $cfdi = Cfdi::newFromString(Cleaner::staticClean($this->fileContents('cfdi33-valid.xml')));

        $cfdiData = (new CfdiDataBuilder())
            ->withXmlResolver($this->createXmlResolver())
            ->build($cfdi->getNode());
        $uuid = $this->strval($cfdiData->timbreFiscalDigital()['UUID']);

        $builder = new Html2PdfBuilder();
        $converter = new Converter($builder);

        $created = $this->filePath('cfdi33-valid.pdf');
        $converter->createPdfAs($cfdiData, $created);
        $this->assertFileExists($created);

        $contents = $this->pdfToText->extract($created);
        $this->assertStringContainsString($uuid, $contents);

        unlink($created);
    }

    public function testConverterWithPaymentData(): void
    {
        $cfdi = Cfdi::newFromString(Cleaner::staticClean($this->fileContents('cfdi33-payment-valid.xml')));

        $cfdiData = (new CfdiDataBuilder())
            ->withXmlResolver($this->createXmlResolver())
            ->build($cfdi->getNode());

        $pagos = $cfdiData->comprobante()->searchNodes('cfdi:Complemento', 'pago10:Pagos', 'pago10:Pago');

        $pago = $pagos->first();
        if (null === $pago) {
            $this->fail('Specimen does not have a Pago element');
        }

        $doctosRelacionados = $pago->searchNodes('pago10:DoctoRelacionado');

        $doctoRelacionado = $doctosRelacionados->first();
        if (null === $doctoRelacionado) {
            $this->fail('Specimen does not have a DoctoRelacionado element');
        }

        $builder = new Html2PdfBuilder();
        $converter = new Converter($builder);

        $created = $this->filePath('cfdi33-payment-valid.pdf');
        $converter->createPdfAs($cfdiData, $created);
        $this->assertFileExists($created);

        $contents = $this->pdfToText->extract($created);
        $this->assertStringContainsString($this->strval($doctoRelacionado['IdDocumento']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['Serie']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['Folio']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['MonedaDR']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['TipoCambioDR']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['MetodoDePagoDR']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['NumParcialidad']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['ImpPagado']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['ImpSaldoInsoluto']), $contents);
        $this->assertStringContainsString($this->strval($doctoRelacionado['ImpSaldoAnt']), $contents);
        unlink($created);
    }
}
