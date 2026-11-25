<?php

namespace Tests\Unit;

use App\Exceptions\ApplicationException;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\ApplicationService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    private ApplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ApplicationService;
    }

    public function test_apply_fails_when_vacancy_is_paused(): void
    {
        $vacancy = new Vacancy(['title' => 'Dev PHP', 'is_paused' => true]);

        $candidate = Mockery::mock(Candidate::class);
        $candidate->shouldNotReceive('vacancies');

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('pausada');

        $this->service->apply($candidate, $vacancy);
    }

    public function test_apply_fails_when_candidate_already_applied(): void
    {
        $vacancy = new Vacancy(['title' => 'Dev PHP', 'is_paused' => false]);

        $candidate = Mockery::mock(Candidate::class)->shouldIgnoreMissing();
        $candidate->shouldReceive('hasAppliedTo')->once()->with($vacancy)->andReturn(true);
        $candidate->shouldNotReceive('vacancies');

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('já está inscrito');

        $this->service->apply($candidate, $vacancy);
    }

    public function test_apply_attaches_vacancy_to_candidate(): void
    {
        $vacancy = new Vacancy(['title' => 'Dev PHP', 'is_paused' => false]);
        $vacancy->id = 10;

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('attach')->once()->with(10);

        $candidate = Mockery::mock(Candidate::class);
        $candidate->shouldReceive('hasAppliedTo')->once()->andReturn(false);
        $candidate->shouldReceive('vacancies')->once()->andReturn($relation);

        $this->service->apply($candidate, $vacancy);
    }

    public function test_withdraw_detaches_vacancy(): void
    {
        $vacancy = new Vacancy(['title' => 'Dev PHP']);
        $vacancy->id = 10;

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('detach')->once()->with(10)->andReturn(1);

        $candidate = Mockery::mock(Candidate::class);
        $candidate->shouldReceive('vacancies')->once()->andReturn($relation);

        $this->service->withdraw($candidate, $vacancy);
    }

    public function test_withdraw_fails_when_candidate_is_not_applied(): void
    {
        $vacancy = new Vacancy(['title' => 'Dev PHP']);
        $vacancy->id = 10;

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('detach')->once()->with(10)->andReturn(0);

        $candidate = Mockery::mock(Candidate::class)->shouldIgnoreMissing();
        $candidate->shouldReceive('vacancies')->once()->andReturn($relation);

        $this->expectException(ApplicationException::class);

        $this->service->withdraw($candidate, $vacancy);
    }
}