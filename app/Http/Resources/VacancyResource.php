<?php

namespace App\Http\Resources;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vacancy
 */
class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'contract_type' => $this->contract_type->value,
            'contract_type_label' => $this->contract_type->label(),
            'salary' => $this->salary !== null ? (float) $this->salary : null,
            'description' => $this->description,
            'is_paused' => $this->is_paused,
            'candidates_count' => $this->whenCounted('candidates'),
            'candidates' => CandidateResource::collection($this->whenLoaded('candidates')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
