<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders;

use PhpCfdi\CfdiToPdf\CfdiData;

interface BuilderInterface
{
    /**
     * Transform CfdiData contents to a PDF file
     * and store its contents on $destination
     *
     * @param CfdiData $data
     * @param string $destination
     * @return void
     */
    public function build(CfdiData $data, string $destination);
}
