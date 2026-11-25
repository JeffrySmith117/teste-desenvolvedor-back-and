<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Filtro de consulta genérico (padrão "Query Filter").
 *
 * Cada parâmetro recebido é convertido em camelCase e, se existir um método
 * público com esse nome na subclasse, ele é aplicado à query. Isso mantém os
 * controllers enxutos e torna cada filtro testável isoladamente.
 */
abstract class QueryFilter
{
    protected Builder $builder;

    /**
     * Colunas permitidas na ordenação.
     *
     * @var array<int, string>
     */
    protected array $sortable = ['id', 'created_at'];

    protected string $defaultSort = 'created_at';

    protected string $defaultDirection = 'desc';

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(protected array $filters = []) {}

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $method = Str::camel($name);

            if (! $this->isFilterMethod($method) || $this->isBlank($value)) {
                continue;
            }

            $this->{$method}($value);
        }

        $this->applySort();

        return $this->builder;
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * Busca parcial, sem diferenciar maiúsculas/minúsculas, portável entre
     * PostgreSQL, MySQL e SQLite.
     */
    protected function whereLike(string $column, string $value): void
    {
        $this->builder->whereRaw(
            "LOWER({$column}) LIKE ?",
            ['%'.mb_strtolower(trim($value)).'%']
        );
    }

    protected function applySort(): void
    {
        $sort = $this->filters['sort'] ?? $this->defaultSort;
        $direction = strtolower((string) ($this->filters['direction'] ?? $this->defaultDirection));

        if (! in_array($sort, $this->sortable, true)) {
            $sort = $this->defaultSort;
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = $this->defaultDirection;
        }

        $this->builder->orderBy($sort, $direction)->orderBy('id', $direction);
    }

    private function isFilterMethod(string $method): bool
    {
        if (in_array($method, ['apply', 'filters', 'sort', 'direction'], true)) {
            return false;
        }

        if (! method_exists($this, $method)) {
            return false;
        }

        return (new \ReflectionMethod($this, $method))->isPublic();
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
