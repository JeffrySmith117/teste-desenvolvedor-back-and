<?php

namespace App\Http\Requests\Vacancy;

class UpdateVacancyRequest extends VacancyRequest
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
