<?php

namespace Tests\Unit;

use App\Support\PerPage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PerPageTest extends TestCase
{
    public function test_default_is_twenty_items(): void
    {
        $this->assertSame(20, PerPage::DEFAULT);
        $this->assertSame(20, PerPage::resolve(null));
    }

    #[DataProvider('allowedValues')]
    public function test_allowed_values_are_accepted(mixed $input, int $expected): void
    {
        $this->assertSame($expected, PerPage::resolve($input));
    }

    #[DataProvider('invalidValues')]
    public function test_invalid_values_fall_back_to_default(mixed $input): void
    {
        $this->assertSame(PerPage::DEFAULT, PerPage::resolve($input));
    }

    public static function allowedValues(): array
    {
        return [
            'inteiro 10' => [10, 10],
            'string 50' => ['50', 50],
            'string 100' => ['100', 100],
        ];
    }

    public static function invalidValues(): array
    {
        return [
            'fora da lista' => ['3'],
            'muito grande' => [1000],
            'texto' => ['abc'],
            'negativo' => [-20],
            'vazio' => [''],
        ];
    }
}
