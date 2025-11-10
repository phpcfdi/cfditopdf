<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Builders\HtmlTranslators;

use League\Plates\Engine as PlatesEngine;
use PhpCfdi\CfdiToPdf\CfdiData;

class PlatesHtmlTranslator implements HtmlTranslatorInterface
{
    public function __construct(
        private readonly string $directory,
        private readonly string $template,
    ) {
    }

    public function translate(CfdiData $cfdiData): string
    {
        // __DIR__ is src/Builders
        $plates = new PlatesEngine($this->directory());
        return $plates->render($this->template(), ['cfdiData' => $cfdiData]);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function template(): string
    {
        return $this->template;
    }
}
