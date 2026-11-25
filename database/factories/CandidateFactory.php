<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('(86) 9####-####'),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d'),
            'linkedin_url' => 'https://www.linkedin.com/in/'.Str::slug($name).'-'.fake()->numberBetween(100, 999),
            'summary' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
