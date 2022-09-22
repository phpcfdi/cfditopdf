<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use CfdiUtils\Internals\TemporaryFile;
use PhpCfdi\CfdiToPdf\Builders\BuilderInterface;

class Converter
{
    /** @var BuilderInterface */
    private $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param CfdiData $cfdiData
     * @param string $destination
     * @return void
     */
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
