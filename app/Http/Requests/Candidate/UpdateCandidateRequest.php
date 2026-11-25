<?php

namespace App\Http\Requests\Candidate;

class UpdateCandidateRequest extends CandidateRequest
{
    /**
     * Na API, permite atualização parcial (PATCH) enviando só alguns campos.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        if ($this->isMethod('patch')) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = array_merge(['sometimes'], $fieldRules);
            }
        }

        return $rules;
    }
}
