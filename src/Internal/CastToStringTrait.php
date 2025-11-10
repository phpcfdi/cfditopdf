<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Internal;

use Stringable;

/**
 * @internal
 */
trait CastToStringTrait
{
    private function strval(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (null === $value || is_scalar($value)) {
            return strval($value);
        }
        if (is_object($value) && is_callable([$value, '__toString'])) {
            /** @phpstan-var Stringable $value */
            return $value->__toString();
        }
        return '';
    }
}
