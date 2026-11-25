<?php

namespace App\Exceptions;

use App\Models\Candidate;
use App\Models\Vacancy;
use DomainException;

/**
 * Regra de negócio violada ao inscrever um candidato em uma vaga.
 */
class ApplicationException extends DomainException
{
    public static function vacancyPaused(Vacancy $vacancy): self
    {
        return new self("A vaga \"{$vacancy->title}\" está pausada e não aceita novas inscrições.");
    }

    public static function alreadyApplied(Candidate $candidate, Vacancy $vacancy): self
    {
        return new self("{$candidate->name} já está inscrito(a) na vaga \"{$vacancy->title}\".");
    }

    public static function notApplied(Candidate $candidate, Vacancy $vacancy): self
    {
        return new self("{$candidate->name} não está inscrito(a) na vaga \"{$vacancy->title}\".");
    }
}
