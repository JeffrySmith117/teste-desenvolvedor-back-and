<?php

namespace App\Http\Requests\Candidate;

use App\Models\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Regras compartilhadas entre criação e edição de candidatos.
 */
abstract class CandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('email')) {
            $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Candidate|null $candidate */
        $candidate = $this->route('candidate');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('candidates', 'email')->ignore($candidate?->getKey()),
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9()+\-\s]+$/'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'phone' => 'telefone',
            'birth_date' => 'data de nascimento',
            'linkedin_url' => 'LinkedIn',
            'summary' => 'resumo profissional',
        ];
    }
}
