<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Utils;

class TemporaryFilename
{
    /** @var string */
    private $filename;

    /** @var bool */
    private $deleteOnDestruct;

    public function __construct(string $dir = '', string $prefix = '', bool $deleteOnDestruct = true)
    {
        $this->deleteOnDestruct = $deleteOnDestruct;
        $this->filename = (string) tempnam($dir, $prefix);
        if ('' === $this->filename) {
            throw new \RuntimeException('Cannot create the temporary filename');
        }
    }

    public function __destruct()
    {
        if ($this->deleteOnDestruct) {
            $this->unlink();
        }
    }

    public function __toString()
    {
        return $this->filename();
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function deleteOnDestruct(): bool
    {
        return $this->deleteOnDestruct;
    }

    public function setDeleteOnDestruct(bool $deleteOnDestruct)
    {
        $this->deleteOnDestruct = $deleteOnDestruct;
    }

    public function unlink()
    {
        if (file_exists($this->filename) && ! is_dir($this->filename)) {
            unlink($this->filename);
        }
    }
}
