<?php

namespace Tests\Feature\Api;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_index_returns_paginated_json(): void
    {
        Candidate::factory()->count(22)->create();

        $this->getJson('/api/candidates')
            ->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJsonPath('meta.total', 22);
    }

    public function test_index_can_be_filtered_by_vacancy(): void
    {
        $vacancy = Vacancy::factory()->create();
        $vacancy->candidates()->attach(Candidate::factory()->count(2)->create());
        Candidate::factory()->count(4)->create();

        $this->getJson("/api/candidates?vacancy_id={$vacancy->id}")
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_candidate_can_be_created(): void
    {
        $this->postJson('/api/candidates', [
            'name' => 'Carlos Pereira',
            'email' => 'carlos@example.com',
            'birth_date' => '1995-03-20',
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Carlos Pereira')
            ->assertJsonPath('data.birth_date', '1995-03-20');
    }

    public function test_creation_is_validated(): void
    {
        Candidate::factory()->create(['email' => 'repetido@example.com']);

        $this->postJson('/api/candidates', ['email' => 'repetido@example.com', 'linkedin_url' => 'abc'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'linkedin_url']);
    }

    public function test_show_includes_vacancies(): void
    {
        $candidate = Candidate::factory()->create();
        $candidate->vacancies()->attach(Vacancy::factory()->count(3)->create());

        $this->getJson("/api/candidates/{$candidate->id}")
            ->assertOk()
            ->assertJsonPath('data.vacancies_count', 3)
            ->assertJsonCount(3, 'data.vacancies');
    }

    public function test_candidate_can_be_partially_updated(): void
    {
        $candidate = Candidate::factory()->create(['email' => 'original@example.com']);

        $this->patchJson("/api/candidates/{$candidate->id}", ['phone' => '(86) 98888-7777'])
            ->assertOk()
            ->assertJsonPath('data.phone', '(86) 98888-7777')
            ->assertJsonPath('data.email', 'original@example.com');
    }

    public function test_candidate_can_be_deleted(): void
    {
        $candidate = Candidate::factory()->create();

        $this->deleteJson("/api/candidates/{$candidate->id}")->assertNoContent();

        $this->assertModelMissing($candidate);
    }

    public function test_candidates_can_be_deleted_in_bulk(): void
    {
        $candidates = Candidate::factory()->count(3)->create();

        $this->deleteJson('/api/candidates/bulk', ['ids' => $candidates->pluck('id')->all()])
            ->assertOk()
            ->assertJsonPath('deleted', 3);

        $this->assertDatabaseCount('candidates', 0);
    }
}
