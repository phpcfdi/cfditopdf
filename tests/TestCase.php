<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Tests;

use CfdiUtils\XmlResolver\XmlResolver;
use RuntimeException;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function filePath(string $filename): string
    {
        return __DIR__ . '/_files/' . $filename;
    }

    public static function fileContents(string $filename): string
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        return strval(@file_get_contents(static::filePath($filename))) ?: '';
    }

    public static function fileTemporaryFile(): string
    {
        $temporaryFile = tempnam('', '');
        if (false === $temporaryFile) {
            throw new RuntimeException('Unable to create a temporary file');
        }
        return $temporaryFile;
    }

    public static function createXmlResolver(): XmlResolver
    {
        $resourcesFolder = __DIR__ . '/_files/external-resources';
        return new XmlResolver($resourcesFolder);
    }
}
