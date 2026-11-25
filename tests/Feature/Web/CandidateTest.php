<?php

namespace Tests\Feature\Web;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ana Lima',
            'email' => 'ana@example.com',
            'phone' => '(86) 99999-0000',
            'birth_date' => '1998-05-10',
            'linkedin_url' => 'https://www.linkedin.com/in/ana-lima',
            'summary' => 'Desenvolvedora PHP.',
        ], $overrides);
    }

    public function test_index_paginates_twenty_items_by_default(): void
    {
        Candidate::factory()->count(25)->create();

        $this->get(route('candidates.index'))
            ->assertOk()
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->count() === 20 && $page->total() === 25);
    }

    public function test_index_can_be_filtered_by_name_and_email(): void
    {
        Candidate::factory()->create(['name' => 'Ana Lima', 'email' => 'ana@empresa.com']);
        Candidate::factory()->create(['name' => 'Ana Souza', 'email' => 'ana.souza@gmail.com']);
        Candidate::factory()->create(['name' => 'Bruno Costa']);

        $this->get(route('candidates.index', ['name' => 'ana', 'email' => 'empresa']))
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->total() === 1);
    }

    public function test_index_can_be_filtered_by_vacancy(): void
    {
        $vacancy = Vacancy::factory()->create();
        $vacancy->candidates()->attach(Candidate::factory()->count(2)->create());
        Candidate::factory()->count(3)->create();

        $this->get(route('candidates.index', ['vacancy_id' => $vacancy->id]))
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->total() === 2);
    }

    public function test_pages_are_displayed(): void
    {
        $candidate = Candidate::factory()->create();

        $this->get(route('candidates.create'))->assertOk();
        $this->get(route('candidates.show', $candidate))->assertOk()->assertSee($candidate->name);
        $this->get(route('candidates.edit', $candidate))->assertOk();
    }

    public function test_candidate_can_be_created_and_email_is_normalized(): void
    {
        $this->post(route('candidates.store'), $this->payload(['email' => '  ANA@Example.com ']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('candidates', ['name' => 'Ana Lima', 'email' => 'ana@example.com']);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('candidates.store'), [])->assertSessionHasErrors(['name', 'email']);

        $this->assertDatabaseCount('candidates', 0);
    }

    public function test_field_types_are_validated(): void
    {
        $this->post(route('candidates.store'), $this->payload([
            'email' => 'nao-e-email',
            'birth_date' => now()->addYear()->format('Y-m-d'),
            'linkedin_url' => 'linkedin',
            'phone' => 'abc',
        ]))->assertSessionHasErrors(['email', 'birth_date', 'linkedin_url', 'phone']);
    }

    public function test_email_must_be_unique(): void
    {
        Candidate::factory()->create(['email' => 'ana@example.com']);

        $this->post(route('candidates.store'), $this->payload())->assertSessionHasErrors('email');

        $this->assertDatabaseCount('candidates', 1);
    }

    public function test_candidate_can_be_updated_keeping_own_email(): void
    {
        $candidate = Candidate::factory()->create(['email' => 'ana@example.com']);

        $this->put(route('candidates.update', $candidate), $this->payload(['name' => 'Ana Lima Atualizada']))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('candidates.show', $candidate));

        $this->assertSame('Ana Lima Atualizada', $candidate->fresh()->name);
    }

    public function test_candidate_can_be_deleted_with_its_applications(): void
    {
        $candidate = Candidate::factory()->create();
        $candidate->vacancies()->attach(Vacancy::factory()->create());

        $this->delete(route('candidates.destroy', $candidate))->assertRedirect(route('candidates.index'));

        $this->assertModelMissing($candidate);
        $this->assertDatabaseCount('applications', 0);
    }

    public function test_candidates_can_be_deleted_in_bulk(): void
    {
        $candidates = Candidate::factory()->count(4)->create();

        $this->delete(route('candidates.bulk-destroy'), ['ids' => $candidates->take(3)->pluck('id')->all()])
            ->assertRedirect();

        $this->assertDatabaseCount('candidates', 1);
    }
}
