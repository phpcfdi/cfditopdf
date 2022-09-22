<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Script;

use CfdiUtils\Nodes\XmlNodeUtils;
use CfdiUtils\XmlResolver\XmlResolver;
use DirectoryIterator;
use Generator;
use LogicException;
use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiDataBuilder;
use PhpCfdi\CfdiToPdf\Converter;
use RuntimeException;
use SplFileInfo;

class ConvertScript
{
    /**
     * @param ConvertOptions $options
     * @return void
     */
    public function run(ConvertOptions $options): void
    {
        $source = $this->openSource($options->inputFile(), $options->doCleanInput());

        $comprobante = XmlNodeUtils::nodeFromXmlString($source);
        $cfdiData = $this->createCfdiDataBuilder()
            ->withXmlResolver($this->createXmlResolver($options->resolverLocation()))
            ->build($comprobante);

        $converter = $this->defaultConverter();

        $fontsDirectory = $options->fontsDirectory();
        $removeFontsDirectory = false;
        if ($this->executionIsFromPhar() && '' === $fontsDirectory) {
            $fontsDirectory = $this->extractFontsToTemporaryFolder();
            $removeFontsDirectory = true;
        }
        if ('' !== $fontsDirectory) {
            $fontsDirectory = rtrim($fontsDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if (! define('K_PATH_FONTS', $fontsDirectory)) {
                throw new LogicException("Unable to define K_PATH_FONTS as $fontsDirectory");
            }
        }

        $converter->createPdfAs($cfdiData, $options->outputFile());

        if ($removeFontsDirectory) {
            $this->recursiveRemove($fontsDirectory);
        }
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

    private function executionIsFromPhar(): bool
    {
        return 'phar://' === substr(__FILE__, 0, 7);
    }

    private function extractFontsToTemporaryFolder(): string
    {
        $source = __DIR__ . '/../../vendor/tecnickcom/tcpdf/fonts';
        $temporaryFolder = tempnam('', '');
        if (false === $temporaryFolder) {
            throw new RuntimeException('Unable to create a temporary name');
        }
        unlink($temporaryFolder);
        $this->recursiveCopy($source, $temporaryFolder);
        return $temporaryFolder;
    }

    private function recursiveCopy(string $sourceDirectory, string $destinationDirectory): void
    {
        mkdir($destinationDirectory);
        /** @var SplFileInfo $origin */
        foreach ($this->readDirectory($sourceDirectory) as $origin) {
            $destination = $destinationDirectory . DIRECTORY_SEPARATOR . $origin->getBasename();
            if ($origin->isFile()) {
                copy($origin->getPathname(), $destination);
            }
            if ($origin->isDir()) {
                $this->recursiveCopy($origin->getPathname(), $destination);
            }
        }
    }

    private function recursiveRemove(string $directory): void
    {
        /** @var SplFileInfo $current */
        foreach ($this->readDirectory($directory) as $current) {
            if ($current->isFile()) {
                unlink($current->getPathname());
            }
            if ($current->isDir()) {
                $this->recursiveRemove($current->getPathname());
            }
        }
        rmdir($directory);
    }

    /**
     * @param string $directory
     * @return Generator
     */
    private function readDirectory(string $directory): Generator
    {
        $directoryIterator = new DirectoryIterator($directory);
        foreach ($directoryIterator as $splFileInfo) {
            if ($splFileInfo->isDot()) {
                continue;
            }

            yield $splFileInfo;
        }
    }
}
