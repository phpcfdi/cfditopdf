<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\Unit\Builders;

use PhpCfdi\CfdiToPdf\Builders\BuilderInterface;
use PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder;
use PhpCfdi\CfdiToPdf\CfdiData;
use PhpCfdi\CfdiToPdf\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/** @covers \PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder */
class Html2PdfBuilderTest extends TestCase
{
    public function testImplementsBuilderInterface(): void
    {
        $builder = new Html2PdfBuilder();
        $this->assertInstanceOf(BuilderInterface::class, $builder);
    }

    public function testBuild(): void
    {
        // it will only test that it store contents received from buildPdf

        /** @var CfdiData&MockObject $fakeCfdiData */
        $fakeCfdiData = $this->createMock(CfdiData::class);
        /** @var Html2PdfBuilder&MockObject $builder */
        $builder = $this->getMockBuilder(Html2PdfBuilder::class)
            ->enableOriginalConstructor()
            ->onlyMethods(['buildPdf'])
            ->getMock();
        $builder->expects($this->once())->method('buildPdf')->willReturn('foo');

        $temporaryFile = $this->fileTemporaryFile();
        $builder->build($fakeCfdiData, $temporaryFile);
        $this->assertStringEqualsFile($temporaryFile, 'foo');
        unlink($temporaryFile);
    }

    public function testBuildPdf(): void
    {
        // it will only test that it will convert html to pdf calling convertNodeToHtml
        /** @var CfdiData&MockObject $fakeCfdiData */
        $fakeCfdiData = $this->createMock(CfdiData::class);
        /** @var Html2PdfBuilder&MockObject $builder */
        $builder = $this->getMockBuilder(Html2PdfBuilder::class)
            ->enableOriginalConstructor()
            ->onlyMethods(['convertNodeToHtml', 'convertHtmlToPdf'])
            ->getMock();
        $builder->expects($this->once())->method('convertNodeToHtml')->willReturn('html');
        $builder->expects($this->once())->method('convertHtmlToPdf')->willReturn('%PDF-1.5');

        $pdfContents = $builder->buildPdf($fakeCfdiData);
        $this->assertStringStartsWith('%PDF-1.5', $pdfContents);
    }
}
