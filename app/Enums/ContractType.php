<?php

namespace App\Enums;

/**
 * Regimes de contratação aceitos por uma vaga.
 */
enum ContractType: string
{
    case Clt = 'clt';
    case Pj = 'pj';
    case Freelancer = 'freelancer';

    public function label(): string
    {
        return match ($this) {
            self::Clt => 'CLT',
            self::Pj => 'Pessoa Jurídica',
            self::Freelancer => 'Freelancer',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Opções prontas para um <select>: [valor => rótulo].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
