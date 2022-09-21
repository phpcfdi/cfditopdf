<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Integration\Script;

use PhpCfdi\CfdiToPdf\Script\ConvertOptions;
use PhpCfdi\CfdiToPdf\Script\ConvertScript;
use PhpCfdi\CfdiToPdf\Tests\PdfToText\PdfToText;
use PhpCfdi\CfdiToPdf\Tests\TestCase;

class ConvertScriptTest extends TestCase
{
    /** @var string */
    private $temporaryFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->temporaryFile = $this->fileTemporaryFile();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->temporaryFile)) {
            unlink($this->temporaryFile);
        }
        parent::tearDown();
    }

    public function testRun(): void
    {
        $outputFile = $this->temporaryFile;
        $options = ConvertOptions::createFromArguments([
            $this->filePath('cfdi33-valid.xml'), // input file
            $outputFile, // output file
            '-l', // set xmlRetriever location, next argument is the path
            $this->filePath('/../../build/resources'),
        ]);
        $script = new ConvertScript();
        $script->run($options);
        $this->assertFileExists($outputFile);
        $this->assertGreaterThan(0, filesize($outputFile));

        $contents = (new PdfToText())->extract($outputFile);
        $this->assertStringContainsString('9FB6ED1A-5F37-4FEF-980A-7F8C83B51894', $contents);
    }
}
