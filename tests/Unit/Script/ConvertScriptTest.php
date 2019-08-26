<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit\Script;

use PhpCfdi\CfdiToPdf\Script\ConvertScript;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class ConvertScriptTest extends TestCase
{
    public function testOpenFileWithEmptyPath()
    {
        $script = new ConvertScript();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Did not provide an input file');
        $script->openSource('', false);
    }

    public function testOpenFileWithNonExistent()
    {
        $inputfile = __DIR__ . '/non-existent';
        $script = new ConvertScript();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The file $inputfile does not exists");
        $script->openSource($inputfile, false);
    }

    public function testOpenFileWithDirectory()
    {
        $inputfile = __DIR__;
        $script = new ConvertScript();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The path $inputfile is not a file");
        $script->openSource($inputfile, false);
    }

    public function testOpenFileWithEmptyContent()
    {
        $inputfile = $this->filePath('empty-file');
        $script = new ConvertScript();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The file $inputfile is empty");
        $script->openSource($inputfile, false);
    }

    public function testOpenFileWithClean()
    {
        /** @var ConvertScript&MockObject $script */
        $script = $this->getMockBuilder(ConvertScript::class)
            ->setMethods(['cleanSource'])
            ->getMock();
        $script->expects($this->once())
            ->method('cleanSource')->willReturn($this->fileContents('cfdi33-valid.xml'));
        $inputfile = $this->filePath('cfdi33-valid.xml');
        $script->openSource($inputfile, true);
        $script->openSource($inputfile, false);
    }
}
