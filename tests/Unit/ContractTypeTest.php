<?php

namespace Tests\Unit;

use App\Enums\ContractType;
use PHPUnit\Framework\TestCase;

class ContractTypeTest extends TestCase
{
    public function test_it_exposes_the_three_contract_types(): void
    {
        $this->assertSame(['clt', 'pj', 'freelancer'], ContractType::values());
    }

    public function test_each_type_has_a_human_readable_label(): void
    {
        $this->assertSame('CLT', ContractType::Clt->label());
        $this->assertSame('Pessoa Jurídica', ContractType::Pj->label());
        $this->assertSame('Freelancer', ContractType::Freelancer->label());
    }

    public function test_options_map_values_to_labels(): void
    {
        $this->assertSame([
            'clt' => 'CLT',
            'pj' => 'Pessoa Jurídica',
            'freelancer' => 'Freelancer',
        ], ContractType::options());
    }

    public function test_invalid_value_is_rejected(): void
    {
        $this->assertNull(ContractType::tryFrom('estagio'));
    }
}