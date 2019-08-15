<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use CfdiUtils\Internals\TemporaryFile;
use PhpCfdi\CfdiToPdf\Builders\BuilderInterface;

class Converter
{
    /** BuilderInterface */
    private $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function createPdfAs(CfdiData $cfdiData, string $destination)
    {
        $temporary = $this->createPdf($cfdiData);
        copy($temporary, $destination);
        unlink($temporary);
    }

    public function createPdf(CfdiData $cfdiData): string
    {
        $temporary = TemporaryFile::create();
        $filename = $temporary->getPath();
        $this->builder->build($cfdiData, $filename);
        return $filename;
    }
}
