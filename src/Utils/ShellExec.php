<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Utils;

class ShellExec
{
    /** @var string */
    private $command;

    /** @var string[] */
    private $output;

    /** @var string */
    private $lastLine;

    /** @var int */
    private $exitStatus;

    /**
     * ShellExec constructor.
     * @param string $command
     * @param string[] $output
     * @param int $exitStatus
     * @param string $lastLine
     */
    public function __construct(string $command, array $output = [], int $exitStatus = -1, string $lastLine = '')
    {
        $this->command = $command;
        $this->output = $output;
        $this->exitStatus = $exitStatus;
        $this->lastLine = $lastLine;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function output(): array
    {
        return $this->output;
    }

    public function lastLine(): string
    {
        return $this->lastLine;
    }

    public function exitStatus(): int
    {
        return $this->exitStatus;
    }

    public function exec()
    {
        $output = [];
        $exitStatus = -1;
        $lastline = exec($this->command(), $output, $exitStatus);

        $this->output = $output;
        $this->exitStatus = $exitStatus;
        $this->lastLine = $lastline;
    }

    public static function run(string $command): self
    {
        $shellExec = new self($command);
        $shellExec->exec();
        return $shellExec;
    }
}
