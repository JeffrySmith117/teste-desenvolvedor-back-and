<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\Candidate;
use App\Models\Vacancy;

/**
 * Regras de negócio das inscrições de candidatos em vagas.
 * Usado tanto pela interface web quanto pela API.
 */
class ApplicationService
{
    /**
     * @throws ApplicationException
     */
    public function apply(Candidate $candidate, Vacancy $vacancy): void
    {
        if (! $vacancy->acceptsApplications()) {
            throw ApplicationException::vacancyPaused($vacancy);
        }

        if ($candidate->hasAppliedTo($vacancy)) {
            throw ApplicationException::alreadyApplied($candidate, $vacancy);
        }

        $candidate->vacancies()->attach($vacancy->getKey());
    }

    /**
     * @throws ApplicationException
     */
    public function withdraw(Candidate $candidate, Vacancy $vacancy): void
    {
        $removed = $candidate->vacancies()->detach($vacancy->getKey());

        if ($removed === 0) {
            throw ApplicationException::notApplied($candidate, $vacancy);
        }
    }
}
