<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders;

use \Spipu\Html2Pdf\Html2Pdf as Html2Pdf;
use League\Plates\Engine as PlatesEngine;
use PhpCfdi\CfdiToPdf\CfdiData;

class Html2PdfBuilder implements BuilderInterface
{
    public function build(CfdiData $data, string $destination)
    {
        $html2Pdf = new Html2Pdf('P', 'Letter', 'es', true, 'UTF-8', [10, 10, 10, 10]);
        $html2Pdf->writeHTML($this->convertNodeToHtml($data));
        // don't do it directly since output method check that the file extension is pdf
        file_put_contents($destination, $html2Pdf->output('null.pdf', 'S'));
    }

    public function convertNodeToHtml(CfdiData $cfdiData)
    {
        // __DIR__ is src/Builders
        $plates = new PlatesEngine(dirname(__DIR__, 2) . '/templates/');
        return $plates->render('generic', ['cfdiData' => $cfdiData]);
    }
}
