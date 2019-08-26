<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit\Script;

use PhpCfdi\CfdiToPdf\Script\ConvertOptions;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use RuntimeException;

class ConvertOptionsTest extends TestCase
{
    public function testConstructor()
    {
        $resolverLocation = 'resolver-location';
        $inputFile = 'input-file';
        $outputFile = 'output-file';
        $doCleanInput = true;
        $askForHelp = false;
        $askForVersion = false;
        $options = new ConvertOptions(
            $resolverLocation,
            $doCleanInput,
            $inputFile,
            $outputFile,
            $askForHelp,
            $askForVersion
        );
        $this->assertSame($resolverLocation, $options->resolverLocation());
        $this->assertSame($inputFile, $options->inputFile());
        $this->assertSame($outputFile, $options->outputFile());
        $this->assertSame($doCleanInput, $options->doCleanInput());
        $this->assertSame($askForHelp, $options->askForHelp());
        $this->assertSame($askForVersion, $options->askForVersion());
    }

    public function testCreateFromArgumentsDefaults()
    {
        $options = ConvertOptions::createFromArguments([]);
        $this->assertSame('', $options->resolverLocation());
        $this->assertSame('', $options->inputFile());
        $this->assertSame('', $options->outputFile());
        $this->assertSame(true, $options->doCleanInput());
        $this->assertSame(false, $options->askForHelp());
        $this->assertSame(false, $options->askForVersion());
    }

    public function testCreateFromArgumentsResolverLocation()
    {
        $options = ConvertOptions::createFromArguments(['-l', 'foo']);
        $this->assertSame('foo', $options->resolverLocation());
    }

    public function testCreateFromArgumentsResolverLocationWithoutDirectory()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The resource location parameter does not contains an argument');
        ConvertOptions::createFromArguments(['-l']);
    }

    public function testCreateFromArgumentsInputOutput()
    {
        $options = ConvertOptions::createFromArguments(['input-file', '-l', '/location', 'output-file']);
        $this->assertSame('input-file', $options->inputFile());
        $this->assertSame('output-file', $options->outputFile());
    }

    public function testCreateFromArgumentsWithoutOutput()
    {
        $options = ConvertOptions::createFromArguments(['input-file']);
        $this->assertSame('input-file', $options->inputFile());
        $this->assertSame('input-file.pdf', $options->outputFile());
    }

    public function testCreateFromArgumentsDirty()
    {
        $this->assertTrue(ConvertOptions::createFromArguments([])->doCleanInput());
        $this->assertFalse(ConvertOptions::createFromArguments(['-d'])->doCleanInput());
        $this->assertFalse(ConvertOptions::createFromArguments(['--dirty'])->doCleanInput());
    }

    public function testCreateFromArgumentsAskForHelp()
    {
        $this->assertFalse(ConvertOptions::createFromArguments([])->askForHelp());
        $this->assertTrue(ConvertOptions::createFromArguments(['-h'])->askForHelp());
        $this->assertTrue(ConvertOptions::createFromArguments(['--help'])->askForHelp());
    }

    public function testCreateFromArgumentsAskForVersion()
    {
        $this->assertFalse(ConvertOptions::createFromArguments([])->askForVersion());
        $this->assertTrue(ConvertOptions::createFromArguments(['-v'])->askForVersion());
        $this->assertTrue(ConvertOptions::createFromArguments(['--version'])->askForVersion());
    }

    public function testCreateFromArgumentsWithExtraParameters()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unexpected parameter 'extra'");
        ConvertOptions::createFromArguments(['in', 'out', 'extra']);
    }
}
