<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Internal;

use Stringable;

/**
 * @internal
 */
trait CastToStringTrait
{
    /** @param mixed $value */
    private function strval($value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (null === $value || is_scalar($value) || (is_object($value) && is_callable([$value, '__toString']))) {
            /** @phpstan-var null|scalar|Stringable $value */
            return strval($value);
        }
        return '';
    }
}
