<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Script;

use CfdiUtils\Cleaner\Cleaner;
use CfdiUtils\Nodes\XmlNodeUtils;
use CfdiUtils\XmlResolver\XmlResolver;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Converter;
use RuntimeException;

class ConvertScript
{
    /**
     * @param ConvertOptions $options
     * @return void
     */
    public function run(ConvertOptions $options)
    {
        $source = $this->openSource($options->inputFile(), $options->doCleanInput());

        $comprobante = XmlNodeUtils::nodeFromXmlString($source);
        $cfdiData = $this->createCfdiDataBuilder()
            ->withXmlResolver($this->createXmlResolver($options->resolverLocation()))
            ->build($comprobante);

        $converter = $this->defaultConverter();
        $converter->createPdfAs($cfdiData, $options->outputFile());
    }

    public function openSource(string $inputfile, bool $doCleanInput): string
    {
        if ('' === $inputfile) {
            throw new RuntimeException('Did not provide an input file');
        }
        $filename = (string) realpath($inputfile);
        if ('' === $filename) {
            throw new RuntimeException("The file $inputfile does not exists");
        }
        if (! is_file($filename)) {
            throw new RuntimeException("The path $inputfile is not a file");
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $source = strval(@file_get_contents($filename));
        if ('' === $source) {
            throw new RuntimeException("The file $inputfile is empty");
        }
        if ($doCleanInput) {
            $source = $this->cleanSource($source);
        }

        return $source;
    }

    public function cleanSource(string $source): string
    {
        return Cleaner::staticClean($source);
    }

    public function createXmlResolver(string $resolverLocation): XmlResolver
    {
        return new XmlResolver($resolverLocation);
    }

    public function createCfdiDataBuilder(): CfdiDataBuilder
    {
        return new CfdiDataBuilder();
    }

    public function defaultConverter(): Converter
    {
        return new Converter(new Html2PdfBuilder());
    }
}
