<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Inscreve cada candidato em 0 a 3 vagas abertas.
     */
    public function run(): void
    {
        $openVacancyIds = Vacancy::query()->open()->pluck('id');

        if ($openVacancyIds->isEmpty()) {
            return;
        }

        Candidate::query()->each(function (Candidate $candidate) use ($openVacancyIds) {
            $amount = random_int(0, min(3, $openVacancyIds->count()));

            if ($amount === 0) {
                return;
            }

            $candidate->vacancies()->syncWithoutDetaching(
                $openVacancyIds->random($amount)->all()
            );
        });
    }
}
