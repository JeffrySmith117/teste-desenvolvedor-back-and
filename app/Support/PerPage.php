<?php

namespace App\Support;

/**
 * Resolve o número de itens por página escolhido pelo usuário,
 * aceitando apenas valores de uma lista permitida.
 */
final class PerPage
{
    public const DEFAULT = 20;

    /** @var array<int, int> */
    public const OPTIONS = [10, 20, 50, 100];

    public static function resolve(mixed $value): int
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);

        return in_array($value, self::OPTIONS, true) ? $value : self::DEFAULT;
    }
}
