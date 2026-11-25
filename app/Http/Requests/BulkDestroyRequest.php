<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Exclusão em massa: recebe uma lista de IDs.
 */
class BulkDestroyRequest extends FormRequest
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
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['integer', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Selecione ao menos um item para excluir.',
            'ids.min' => 'Selecione ao menos um item para excluir.',
        ];
    }

    /**
     * @return array<int, int>
     */
    public function ids(): array
    {
        return array_map('intval', $this->validated('ids'));
    }
}
