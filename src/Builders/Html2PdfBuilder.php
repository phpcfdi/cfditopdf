<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders;

use PhpCfdi\CfdiToPdf\Builders\HtmlTranslators\HtmlTranslatorInterface;
use PhpCfdi\CfdiToPdf\CfdiData;
use RuntimeException;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class Html2PdfBuilder implements BuilderInterface
{
    /** @var HtmlTranslators\HtmlTranslatorInterface */
    private $htmlTranslator;

    /**
     * Html2PdfBuilder constructor.
     *
     * @param HtmlTranslatorInterface|null $htmlTranslator If NULL will use a generic translator
     */
    public function __construct(HtmlTranslatorInterface $htmlTranslator = null)
    {
        if (null === $htmlTranslator) {
            $htmlTranslator = new HtmlTranslators\PlatesHtmlTranslator(
                dirname(__DIR__, 2) . '/templates/', // __DIR__ is src/Builders
                'generic'
            );
        }
        $this->htmlTranslator = $htmlTranslator;
    }

    public function build(CfdiData $data, string $destination)
    {
        file_put_contents($destination, $this->buildPdf($data));
    }

    /**
     * Transforms CfdiData to Pdf string
     *
     * @param CfdiData $data
     * @return string
     */
    public function buildPdf(CfdiData $data): string
    {
        $html = $this->convertNodeToHtml($data);
        $output = $this->convertHtmlToPdf($html);
        return $output;
    }

    public function convertHtmlToPdf(string $html): string
    {
        // don't do it directly since Html2Pdf::output check that the file extension is pdf
        try {
            $html2Pdf = new Html2Pdf('P', 'Letter', 'es', true, 'UTF-8', [10, 10, 10, 10]);
            $html2Pdf->writeHTML($html);
            $output = $html2Pdf->output('', 'S');
            return $output;
        } catch (Html2PdfException $exception) {
            /** @codeCoverageIgnore don't know how to invoke this exception on Html2Pdf */
            throw new RuntimeException('Unable to convert CFDI', 0, $exception);
        }
    }

    public function convertNodeToHtml(CfdiData $cfdiData): string
    {
        return $this->htmlTranslator->translate($cfdiData);
    }
}
