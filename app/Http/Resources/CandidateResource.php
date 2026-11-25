<?php

namespace App\Http\Resources;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Candidate
 */
class CandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'linkedin_url' => $this->linkedin_url,
            'summary' => $this->summary,
            'vacancies_count' => $this->whenCounted('vacancies'),
            'vacancies' => VacancyResource::collection($this->whenLoaded('vacancies')),
            'applied_at' => $this->whenPivotLoaded('applications', fn () => $this->pivot->created_at?->toIso8601String()),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
