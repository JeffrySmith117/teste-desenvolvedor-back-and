<?php

namespace App\Filters;

use App\Enums\ContractType;

class VacancyFilter extends QueryFilter
{
    protected array $sortable = ['id', 'title', 'company', 'location', 'contract_type', 'salary', 'is_paused', 'created_at'];

    public function id(mixed $value): void
    {
        $this->builder->whereKey((int) $value);
    }

    public function title(string $value): void
    {
        $this->whereLike('title', $value);
    }

    public function company(string $value): void
    {
        $this->whereLike('company', $value);
    }

    public function location(string $value): void
    {
        $this->whereLike('location', $value);
    }

    public function description(string $value): void
    {
        $this->whereLike('description', $value);
    }

    public function contractType(string $value): void
    {
        if (ContractType::tryFrom($value)) {
            $this->builder->where('contract_type', $value);
        }
    }

    /**
     * status = open | paused
     */
    public function status(string $value): void
    {
        match ($value) {
            'open' => $this->builder->where('is_paused', false),
            'paused' => $this->builder->where('is_paused', true),
            default => null,
        };
    }

    public function salaryMin(mixed $value): void
    {
        if (is_numeric($value)) {
            $this->builder->where('salary', '>=', $value);
        }
    }

    public function salaryMax(mixed $value): void
    {
        if (is_numeric($value)) {
            $this->builder->where('salary', '<=', $value);
        }
    }

    public function createdFrom(string $value): void
    {
        $this->builder->whereDate('created_at', '>=', $value);
    }

    public function createdTo(string $value): void
    {
        $this->builder->whereDate('created_at', '<=', $value);
    }
}
