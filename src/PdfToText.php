<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use PhpCfdi\CfdiToPdf\Utils\ShellExec;

/**
 * Extract the contents of a pdf file using pdftotext (apt-get install poppler-utils)
 */
class PdfToText
{
    private $pdftotext;

    public function __construct(string $pathPdfToText = '')
    {
        if ('' === $pathPdfToText) {
            $pathPdfToText = (string) shell_exec('which pdftotext');
            if ('' === $pathPdfToText) {
                throw new \RuntimeException('pdftotext command was not found');
            }
        }
        $this->pdftotext = $pathPdfToText;
    }

    /**
     * @param string $filename
     * @return string[] file contents
     */
    public function extract(string $filename): array
    {
        $shellExec = ShellExec::run($this->buildCommand($filename));
        if (0 !== $shellExec->exitStatus()) {
            throw new \RuntimeException("Running pdftotext exit with error (exit status: {$shellExec->exitStatus()})");
        }
        return $shellExec->output();
    }

    public function buildCommand(string $pdfFile): string
    {
        return escapeshellcmd($this->pdftotext) . ' -eol unix -raw -q ' . escapeshellarg($pdfFile) . ' -';
    }
}
