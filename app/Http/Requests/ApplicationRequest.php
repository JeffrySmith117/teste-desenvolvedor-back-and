<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Inscrição de um candidato em uma vaga.
 */
class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            'vacancy_id' => ['required', 'integer', 'exists:vacancies,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'candidate_id' => 'candidato',
            'vacancy_id' => 'vaga',
        ];
    }
}
