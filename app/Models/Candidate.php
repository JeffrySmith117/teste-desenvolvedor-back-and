<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Database\Factories\CandidateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $linkedin_url
 * @property string|null $summary
 */
class Candidate extends Model
{
    /** @use HasFactory<CandidateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'linkedin_url',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * @return BelongsToMany<Vacancy, $this>
     */
    public function vacancies(): BelongsToMany
    {
        return $this->belongsToMany(Vacancy::class, 'applications')->withTimestamps();
    }

    public function hasAppliedTo(Vacancy $vacancy): bool
    {
        return $this->vacancies()->whereKey($vacancy->getKey())->exists();
    }

    public function scopeFilter(Builder $query, QueryFilter $filter): Builder
    {
        return $filter->apply($query);
    }
}
