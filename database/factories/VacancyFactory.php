<?php

namespace Database\Factories;

use App\Enums\ContractType;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancy>
 */
class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    private const TITLES = [
        'Desenvolvedor(a) Back-end PHP',
        'Desenvolvedor(a) Full Stack Laravel',
        'Desenvolvedor(a) Front-end',
        'Analista de Suporte',
        'Analista de QA',
        'Engenheiro(a) de Dados',
        'Analista DevOps',
        'Designer UI/UX',
        'Product Owner',
        'Scrum Master',
        'Estagiário(a) de Desenvolvimento',
        'Analista de Banco de Dados',
    ];

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(self::TITLES),
            'company' => fake()->company(),
            'location' => fake()->city().' - '.fake()->stateAbbr(),
            'contract_type' => fake()->randomElement(ContractType::cases()),
            'salary' => fake()->optional(0.8)->randomFloat(2, 1500, 15000),
            'description' => fake()->paragraphs(3, true),
            'is_paused' => false,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn () => ['is_paused' => true]);
    }

    public function open(): static
    {
        return $this->state(fn () => ['is_paused' => false]);
    }

    public function ofType(ContractType $type): static
    {
        return $this->state(fn () => ['contract_type' => $type]);
    }
}
