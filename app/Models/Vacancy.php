<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Filters\QueryFilter;
use Database\Factories\VacancyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $company
 * @property string|null $location
 * @property ContractType $contract_type
 * @property string|null $salary
 * @property string $description
 * @property bool $is_paused
 */
class Vacancy extends Model
{
    /** @use HasFactory<VacancyFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'contract_type',
        'salary',
        'description',
        'is_paused',
    ];

    protected $attributes = [
        'is_paused' => false,
    ];

    protected function casts(): array
    {
        return [
            'contract_type' => ContractType::class,
            'salary' => 'decimal:2',
            'is_paused' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Candidate, $this>
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'applications')->withTimestamps();
    }

    public function acceptsApplications(): bool
    {
        return ! $this->is_paused;
    }

    public function pause(): void
    {
        $this->update(['is_paused' => true]);
    }

    public function resume(): void
    {
        $this->update(['is_paused' => false]);
    }

    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_paused', false);
    }
}
