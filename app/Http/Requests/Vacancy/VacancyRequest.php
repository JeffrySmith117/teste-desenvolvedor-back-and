<?php

namespace App\Http\Requests\Vacancy;

use App\Enums\ContractType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Regras compartilhadas entre criação e edição de vagas.
 */
abstract class VacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_paused')) {
            $this->merge(['is_paused' => $this->boolean('is_paused')]);
        }

        if ($this->input('salary') === '') {
            $this->merge(['salary' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'company' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'contract_type' => ['required', Rule::enum(ContractType::class)],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['required', 'string', 'max:5000'],
            'is_paused' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'company' => 'empresa',
            'location' => 'local',
            'contract_type' => 'tipo de contratação',
            'salary' => 'salário',
            'description' => 'descrição',
            'is_paused' => 'pausada',
        ];
    }
}
