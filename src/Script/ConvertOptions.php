<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Script;

use RuntimeException;

class ConvertOptions
{
    /** @var string */
    private $resolverLocation;

    /** @var bool */
    private $doCleanInput;

    /** @var string */
    private $inputFile;

    /** @var string */
    private $outputFile;

    /** @var bool */
    private $askForHelp;

    /** @var bool */
    private $askForVersion;

    public function __construct(
        string $resolverLocation,
        bool $doCleanInput,
        string $inputFile,
        string $outputFile,
        bool $askForHelp,
        bool $askForVersion
    ) {
        if ('' === $outputFile && '' !== $inputFile) {
            $outputFile = (string) preg_replace('/\.xml$/', '', $inputFile) . '.pdf';
        }

        $this->resolverLocation = $resolverLocation;
        $this->doCleanInput = $doCleanInput;
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->askForHelp = $askForHelp;
        $this->askForVersion = $askForVersion;
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

    /**
     * @param string[] $arguments
     *
     * @return self
     */
    public static function createFromArguments(array $arguments): self
    {
        $askForHelp = false;
        $askForVersion = false;
        $resolverLocation = '';
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
            if (in_array($argument, ['-v', '--version'], true)) {
                $askForVersion = true;
                break;
            }
            if (in_array($argument, ['-d', '--dirty'], true)) {
                $cleanInput = false;
                continue;
            }
            if (in_array($argument, ['-l', '--resource-location'], true)) {
                $i = $i + 1;
                if (! ($i < $count)) {
                    throw new RuntimeException('The resource location parameter does not contains an argument');
                }
                $resolverLocation = $arguments[$i];
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

        return new self($resolverLocation, $cleanInput, $inputFile, $outputFile, $askForHelp, $askForVersion);
    }
}
