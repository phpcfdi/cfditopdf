<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders\HtmlTranslators;

use PhpCfdi\CfdiToPdf\CfdiData;

interface HtmlTranslatorInterface
{
    /**
     * Transform CfdiData contents to HTML content
     *
     * @param CfdiData $cfdiData
     * @return string
     */
    public function translate(CfdiData $cfdiData): string;
}
