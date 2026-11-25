<?php

namespace Tests\Feature\Api;

use App\Enums\ContractType;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Desenvolvedor PHP',
            'company' => 'ACME Tecnologia',
            'location' => 'Remoto',
            'contract_type' => 'pj',
            'salary' => 6000,
            'description' => 'Vaga para desenvolvedor PHP.',
        ], $overrides);
    }

    public function test_index_returns_paginated_json(): void
    {
        Vacancy::factory()->count(25)->create();

        $this->getJson('/api/vacancies')
            ->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJsonPath('meta.per_page', 20)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonStructure(['data' => [['id', 'title', 'company', 'contract_type', 'is_paused', 'candidates_count']], 'links', 'meta']);
    }

    public function test_index_accepts_per_page_and_filters(): void
    {
        Vacancy::factory()->count(12)->create(['contract_type' => ContractType::Freelancer]);
        Vacancy::factory()->count(5)->create(['contract_type' => ContractType::Clt]);

        $this->getJson('/api/vacancies?per_page=10&contract_type=freelancer')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 12);
    }

    public function test_vacancy_can_be_created(): void
    {
        $this->postJson('/api/vacancies', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.title', 'Desenvolvedor PHP')
            ->assertJsonPath('data.contract_type', 'pj')
            ->assertJsonPath('data.contract_type_label', 'Pessoa Jurídica')
            ->assertJsonPath('data.is_paused', false);

        $this->assertDatabaseCount('vacancies', 1);
    }

    public function test_creation_is_validated(): void
    {
        $this->postJson('/api/vacancies', ['contract_type' => 'estagio', 'salary' => 'abc'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'company', 'contract_type', 'salary', 'description']);
    }

    public function test_show_includes_candidates(): void
    {
        $vacancy = Vacancy::factory()->create();
        $vacancy->candidates()->attach(Candidate::factory()->count(2)->create());

        $this->getJson("/api/vacancies/{$vacancy->id}")
            ->assertOk()
            ->assertJsonPath('data.candidates_count', 2)
            ->assertJsonCount(2, 'data.candidates');
    }

    public function test_missing_vacancy_returns_404(): void
    {
        $this->getJson('/api/vacancies/999')->assertNotFound();
    }

    public function test_vacancy_can_be_fully_updated(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->putJson("/api/vacancies/{$vacancy->id}", $this->payload(['title' => 'Novo título']))
            ->assertOk()
            ->assertJsonPath('data.title', 'Novo título');
    }

    public function test_vacancy_can_be_partially_updated(): void
    {
        $vacancy = Vacancy::factory()->create(['company' => 'Empresa Original']);

        $this->patchJson("/api/vacancies/{$vacancy->id}", ['title' => 'Só o título'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Só o título')
            ->assertJsonPath('data.company', 'Empresa Original');
    }

    public function test_vacancy_can_be_deleted(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->deleteJson("/api/vacancies/{$vacancy->id}")->assertNoContent();

        $this->assertModelMissing($vacancy);
    }

    public function test_vacancies_can_be_deleted_in_bulk(): void
    {
        $vacancies = Vacancy::factory()->count(3)->create();

        $this->deleteJson('/api/vacancies/bulk', ['ids' => $vacancies->take(2)->pluck('id')->all()])
            ->assertOk()
            ->assertJsonPath('deleted', 2);

        $this->assertDatabaseCount('vacancies', 1);
    }

    public function test_vacancy_can_be_paused(): void
    {
        $vacancy = Vacancy::factory()->open()->create();

        $this->patchJson("/api/vacancies/{$vacancy->id}/toggle-pause")
            ->assertOk()
            ->assertJsonPath('data.is_paused', true);
    }
}
