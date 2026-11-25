<?php

namespace Tests\Unit;

use App\Enums\ContractType;
use App\Models\Vacancy;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    public function test_open_vacancy_accepts_applications(): void
    {
        $vacancy = new Vacancy(['is_paused' => false]);

        $this->assertTrue($vacancy->acceptsApplications());
    }

    public function test_paused_vacancy_does_not_accept_applications(): void
    {
        $vacancy = new Vacancy(['is_paused' => true]);

        $this->assertFalse($vacancy->acceptsApplications());
    }

    public function test_new_vacancy_is_open_by_default(): void
    {
        $this->assertFalse((new Vacancy)->is_paused);
    }

    public function test_contract_type_is_cast_to_enum(): void
    {
        $vacancy = new Vacancy(['contract_type' => 'pj']);

        $this->assertSame(ContractType::Pj, $vacancy->contract_type);
    }
}