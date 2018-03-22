<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders;

use PhpCfdi\CfdiToPdf\CfdiData;

interface BuilderInterface
{
    public function build(CfdiData $node, string $destination);
}
