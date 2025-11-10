<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests\PdfToText;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Extract the contents of a pdf file using pdftotext (apt-get install poppler-utils)
 */
class PdfToText
{
    private string $pdftotext;

    public function __construct(string $pathPdfToText = '')
    {
        $this->pdftotext = $pathPdfToText ?: 'pdftotext';
    }

    public function exists(): bool
    {
        $process = new Process([$this->pdftotext, '-v']);
        $exitStatus = $process->run();
        if (0 !== $exitStatus) {
            return false;
        }

        return 'pdftotext ' === substr(trim($process->getErrorOutput()), 0, 10);
    }

    /**
     * @return string file contents
     */
    public function extract(string $filename): string
    {
        $process = new Process([$this->pdftotext, '-eol', 'unix', '-raw', '-q', $filename, '-']);
        $exitStatus = $process->run();
        if (0 !== $exitStatus) {
            throw new RuntimeException("Running pdftotext exit with error (exit status: $exitStatus)");
        }
        return $process->getOutput();
    }
}
