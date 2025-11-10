<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Script;

use RuntimeException;

class ConvertOptions
{
    public function __construct(
        private string $resolverLocation,
        private string $fontsDirectory,
        private bool $doCleanInput,
        private string $inputFile,
        private string $outputFile,
        private bool $askForHelp,
        private bool $askForVersion
    ) {
        if ('' === $this->outputFile && '' !== $this->inputFile) {
            $this->outputFile = preg_replace('/\.xml$/', '', $this->inputFile) . '.pdf';
        }
    }

    public function askForHelp(): bool
    {
        return $this->askForHelp;
    }

    public function askForVersion(): bool
    {
        return $this->askForVersion;
    }

    public function inputFile(): string
    {
        return $this->inputFile;
    }

    public function outputFile(): string
    {
        return $this->outputFile;
    }

    public function doCleanInput(): bool
    {
        return $this->doCleanInput;
    }

    public function resolverLocation(): string
    {
        return $this->resolverLocation;
    }

    public function fontsDirectory(): string
    {
        return $this->fontsDirectory;
    }

    /**
     * @param string[] $arguments
     */
    public static function createFromArguments(array $arguments): self
    {
        $askForHelp = false;
        $askForVersion = false;
        $resolverLocation = '';
        $fontsDirectory = '';
        $cleanInput = true;
        $inputFile = '';
        $outputFile = '';

        $count = count($arguments);
        for ($i = 0; $i < $count; $i = $i + 1) {
            $argument = $arguments[$i];
            if (in_array($argument, ['-h', '--help'], true)) {
                $askForHelp = true;
                break;
            }
            if (in_array($argument, ['-V', '--version'], true)) {
                $askForVersion = true;
                break;
            }
            if (in_array($argument, ['-d', '--dirty'], true)) {
                $cleanInput = false;
                continue;
            }
            if (in_array($argument, ['-l', '--resource-location'], true)) {
                $i = $i + 1;
                if ($i >= $count) {
                    throw new RuntimeException('The resource location parameter does not contains an argument');
                }
                $resolverLocation = $arguments[$i];
                continue;
            }
            if (in_array($argument, ['-f', '--fonts-dir'], true)) {
                $i = $i + 1;
                if ($i >= $count) {
                    throw new RuntimeException('The fonts directory parameter does not contains an argument');
                }
                $fontsDirectory = $arguments[$i];
                continue;
            }
            if ('' === $inputFile) {
                $inputFile = $argument;
                continue;
            }
            if ('' === $outputFile) {
                $outputFile = $argument;
                continue;
            }
            throw new RuntimeException("Unexpected parameter '$argument'");
        }

        return new self(
            $resolverLocation,
            $fontsDirectory,
            $cleanInput,
            $inputFile,
            $outputFile,
            $askForHelp,
            $askForVersion,
        );
    }
}
