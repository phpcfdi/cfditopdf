<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use CfdiUtils\Internals\TemporaryFile;
use PhpCfdi\CfdiToPdf\Builders\BuilderInterface;

class Converter
{
    public function __construct(private readonly BuilderInterface $builder)
    {
    }

    public function createPdfAs(CfdiData $cfdiData, string $destination): void
    {
        $this->builder->build($cfdiData, $destination);
    }

    public function createPdf(CfdiData $cfdiData): string
    {
        $temporary = TemporaryFile::create();
        $filename = $temporary->getPath();
        $this->builder->build($cfdiData, $filename);
        return $filename;
    }
}
