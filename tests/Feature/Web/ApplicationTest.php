<?php

namespace Tests\Feature\Web;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_candidate_can_apply_to_open_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->open()->create();

        $this->from(route('vacancies.show', $vacancy))
            ->post(route('applications.store'), ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertRedirect(route('vacancies.show', $vacancy))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('applications', ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id]);
    }

    public function test_candidate_can_apply_to_multiple_vacancies(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancies = Vacancy::factory()->count(3)->open()->create();

        foreach ($vacancies as $vacancy) {
            $this->post(route('applications.store'), ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id]);
        }

        $this->assertSame(3, $candidate->vacancies()->count());
    }

    public function test_candidate_cannot_apply_to_paused_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->paused()->create();

        $this->from(route('vacancies.show', $vacancy))
            ->post(route('applications.store'), ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertRedirect(route('vacancies.show', $vacancy))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_candidate_cannot_apply_twice_to_same_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->open()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->post(route('applications.store'), ['candidate_id' => $candidate->id, 'vacancy_id' => $vacancy->id])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_application_requires_existing_candidate_and_vacancy(): void
    {
        $this->post(route('applications.store'), ['candidate_id' => 999, 'vacancy_id' => 999])
            ->assertSessionHasErrors(['candidate_id', 'vacancy_id']);
    }

    public function test_application_can_be_withdrawn(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->delete(route('applications.destroy', [$candidate, $vacancy]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('applications', 0);
    }
}
