<?php

namespace App\Filters;

class CandidateFilter extends QueryFilter
{
    protected array $sortable = ['id', 'name', 'email', 'phone', 'birth_date', 'created_at'];

    public function id(mixed $value): void
    {
        $this->builder->whereKey((int) $value);
    }

    public function name(string $value): void
    {
        $this->whereLike('name', $value);
    }

    public function email(string $value): void
    {
        $this->whereLike('email', $value);
    }

    public function phone(string $value): void
    {
        $this->whereLike('phone', $value);
    }

    public function linkedinUrl(string $value): void
    {
        $this->whereLike('linkedin_url', $value);
    }

    public function summary(string $value): void
    {
        $this->whereLike('summary', $value);
    }

    public function birthDateFrom(string $value): void
    {
        $this->builder->whereDate('birth_date', '>=', $value);
    }

    public function birthDateTo(string $value): void
    {
        $this->builder->whereDate('birth_date', '<=', $value);
    }

    /**
     * Candidatos inscritos em uma vaga específica.
     */
    public function vacancyId(mixed $value): void
    {
        $this->builder->whereHas('vacancies', fn ($query) => $query->whereKey((int) $value));
    }
}
