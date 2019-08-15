<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests;

use CfdiUtils\Cfdi;
use CfdiUtils\Cleaner\Cleaner;
use CfdiUtils\XmlResolver\XmlResolver;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Converter;
use PhpCfdi\CfdiToPdf\Tests\PdfToText\PdfToText;

class ConverterTest extends CfdiToPdfTestCase
{
    public function testConverter()
    {
        $resourcesFolder = $this->utilAsset('/../../build/resources');
        $sourcefile = $this->utilAsset('cfdi33-valid.xml');

        $cfdi = Cfdi::newFromString(Cleaner::staticClean(strval(file_get_contents($sourcefile))));

        $cfdiData = (new CfdiDataBuilder())
            ->withXmlResolver(new XmlResolver($resourcesFolder))
            ->build($cfdi->getNode());
        $uuid = $cfdiData->timbreFiscalDigital()['UUID'];

        $builder = new Html2PdfBuilder();
        $converter = new Converter($builder);

        $created = $this->utilAsset('cfdi33-valid.pdf');
        $converter->createPdfAs($cfdiData, $created);
        $this->assertFileExists($created);

        $pdfToString = new PdfToText();
        $contents = $pdfToString->extract($created);

        $this->assertContains($uuid, $contents);

        unlink($created);
    }
}
