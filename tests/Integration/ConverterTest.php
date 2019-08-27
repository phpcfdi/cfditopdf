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
}
