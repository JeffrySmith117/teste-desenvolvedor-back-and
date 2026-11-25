<?php

namespace Tests\Feature\Api;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_candidate_can_apply_to_open_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->open()->create();

        $this->postJson('/api/applications', ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertCreated()
            ->assertJsonPath('candidate_id', $candidate->id)
            ->assertJsonPath('vacancy.id', $vacancy->id);

        $this->assertDatabaseHas('applications', ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id]);
    }

    public function test_paused_vacancy_rejects_applications(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->paused()->create(['title' => 'Dev PHP']);

        $this->postJson('/api/applications', ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A vaga "Dev PHP" está pausada e não aceita novas inscrições.');

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_duplicate_application_is_rejected(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->open()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->postJson('/api/applications', ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertUnprocessable();

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_application_payload_is_validated(): void
    {
        $this->postJson('/api/applications', ['candidate_id' => 'abc'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['candidate_id', 'vacancy_id']);
    }

    public function test_application_can_be_withdrawn(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->deleteJson("/api/candidates/{$candidate->id}/vacancies/{$vacancy->id}")->assertNoContent();

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_withdrawing_nonexistent_application_returns_422(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();

        $this->deleteJson("/api/candidates/{$candidate->id}/vacancies/{$vacancy->id}")->assertUnprocessable();
    }
}