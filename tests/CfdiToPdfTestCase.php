<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests;

use PHPUnit\Framework\TestCase;

class CfdiToPdfTestCase extends TestCase
{
    public static function utilAsset(string $filename): string
    {
        return __DIR__ . '/assets/' . $filename;
    }
}
