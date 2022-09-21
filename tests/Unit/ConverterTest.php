<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit;

use PhpCfdi\CfdiToPdf\Builders\BuilderInterface;
use PhpCfdi\CfdiToPdf\CfdiData;
use PhpCfdi\CfdiToPdf\Converter;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \PhpCfdi\CfdiToPdf\Converter
 */
class ConverterTest extends TestCase
{
    public function testCreatePdfToTemporary(): void
    {
        /** @var CfdiData&MockObject $fakeCfdiData */
        $fakeCfdiData = $this->createMock(CfdiData::class);
        /** @var BuilderInterface&MockObject $fakeBuilder */
        $fakeBuilder = $this->createMock(BuilderInterface::class);
        $fakeBuilder->expects($spy = $this->once())->method('build');

        $converter = new Converter($fakeBuilder);
        $temporaryFile = $converter->createPdf($fakeCfdiData);
        $this->assertFileExists($temporaryFile);
        unlink($temporaryFile);

        $this->assertTrue($spy->hasBeenInvoked());
    }

    public function testCreatePdfToFile(): void
    {
        /** @var CfdiData&MockObject $fakeCfdiData */
        $fakeCfdiData = $this->createMock(CfdiData::class);
        /** @var BuilderInterface&MockObject $fakeBuilder */
        $fakeBuilder = $this->createMock(BuilderInterface::class);
        $temporaryFile = 'foo-bar';
        $fakeBuilder->expects($spy = $this->once())->method('build')->with($fakeCfdiData, $temporaryFile);

        $converter = new Converter($fakeBuilder);
        $converter->createPdfAs($fakeCfdiData, $temporaryFile);
        $this->assertTrue($spy->hasBeenInvoked());
    }
}
