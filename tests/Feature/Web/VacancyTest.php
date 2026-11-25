<?php

namespace Tests\Feature\Web;

use App\Enums\ContractType;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class VacancyTest extends TestCase
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
            'title' => 'Desenvolvedor PHP',
            'company' => 'ACME Tecnologia',
            'location' => 'Teresina - PI',
            'contract_type' => 'clt',
            'salary' => '4500.00',
            'description' => 'Vaga para desenvolvedor PHP com Laravel.',
            'is_paused' => '0',
        ], $overrides);
    }

    public function test_index_paginates_twenty_items_by_default(): void
    {
        Vacancy::factory()->count(25)->create();

        $this->get(route('vacancies.index'))
            ->assertOk()
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->count() === 20 && $page->total() === 25);
    }

    public function test_user_can_change_items_per_page(): void
    {
        Vacancy::factory()->count(25)->create();

        $this->get(route('vacancies.index', ['per_page' => 10]))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->count() === 10 && $page->perPage() === 10);
    }

    public function test_index_can_be_filtered_by_title_and_contract_type(): void
    {
        Vacancy::factory()->create(['title' => 'Dev Laravel Pleno', 'contract_type' => ContractType::Pj]);
        Vacancy::factory()->create(['title' => 'Dev Laravel Júnior', 'contract_type' => ContractType::Clt]);
        Vacancy::factory()->count(3)->create(['title' => 'Analista de Suporte', 'contract_type' => ContractType::Pj]);

        $this->get(route('vacancies.index', ['title' => 'laravel', 'contract_type' => 'pj']))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->total() === 1
                && $page->first()->title === 'Dev Laravel Pleno');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        Vacancy::factory()->count(2)->paused()->create();
        Vacancy::factory()->count(3)->open()->create();

        $this->get(route('vacancies.index', ['status' => 'paused']))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->total() === 2);
    }

    public function test_index_can_be_filtered_by_salary_range(): void
    {
        Vacancy::factory()->create(['salary' => 2000]);
        Vacancy::factory()->create(['salary' => 5000]);
        Vacancy::factory()->create(['salary' => 9000]);

        $this->get(route('vacancies.index', ['salary_min' => 3000, 'salary_max' => 6000]))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->total() === 1);
    }

    public function test_create_and_show_pages_are_displayed(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->get(route('vacancies.create'))->assertOk();
        $this->get(route('vacancies.show', $vacancy))->assertOk()->assertSee($vacancy->title);
        $this->get(route('vacancies.edit', $vacancy))->assertOk();
    }

    public function test_vacancy_can_be_created(): void
    {
        $this->post(route('vacancies.store'), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vacancies', [
            'title' => 'Desenvolvedor PHP',
            'contract_type' => 'clt',
            'is_paused' => false,
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('vacancies.store'), [])
            ->assertSessionHasErrors(['title', 'company', 'contract_type', 'description']);

        $this->assertDatabaseCount('vacancies', 0);
    }

    public function test_field_types_are_validated(): void
    {
        $this->post(route('vacancies.store'), $this->payload([
            'contract_type' => 'estagio',
            'salary' => 'muito',
            'title' => str_repeat('a', 151),
        ]))->assertSessionHasErrors(['contract_type', 'salary', 'title']);

        $this->assertDatabaseCount('vacancies', 0);
    }

    public function test_vacancy_can_be_updated(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->put(route('vacancies.update', $vacancy), $this->payload(['title' => 'Título Atualizado', 'contract_type' => 'freelancer']))
            ->assertRedirect(route('vacancies.show', $vacancy));

        $vacancy->refresh();
        $this->assertSame('Título Atualizado', $vacancy->title);
        $this->assertSame(ContractType::Freelancer, $vacancy->contract_type);
    }

    public function test_vacancy_can_be_deleted_with_its_applications(): void
    {
        $vacancy = Vacancy::factory()->create();
        $vacancy->candidates()->attach(Candidate::factory()->create());

        $this->delete(route('vacancies.destroy', $vacancy))->assertRedirect(route('vacancies.index'));

        $this->assertModelMissing($vacancy);
        $this->assertDatabaseCount('applications', 0);
    }

    public function test_vacancies_can_be_deleted_in_bulk(): void
    {
        $vacancies = Vacancy::factory()->count(5)->create();

        $this->delete(route('vacancies.bulk-destroy'), ['ids' => $vacancies->take(3)->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('vacancies', 2);
    }

    public function test_bulk_delete_requires_ids(): void
    {
        Vacancy::factory()->count(2)->create();

        $this->delete(route('vacancies.bulk-destroy'), [])->assertSessionHasErrors('ids');

        $this->assertDatabaseCount('vacancies', 2);
    }

    public function test_vacancy_can_be_paused_and_resumed(): void
    {
        $vacancy = Vacancy::factory()->open()->create();

        $this->patch(route('vacancies.toggle-pause', $vacancy))->assertRedirect();
        $this->assertTrue($vacancy->fresh()->is_paused);

        $this->patch(route('vacancies.toggle-pause', $vacancy))->assertRedirect();
        $this->assertFalse($vacancy->fresh()->is_paused);
    }
}