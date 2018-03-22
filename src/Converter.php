<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

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
        $temporary = new Utils\TemporaryFilename();

        $this->builder->build($cfdiData, $temporary->filename());

        $temporary->setDeleteOnDestruct(false);
        return $temporary->filename();
    }
}
