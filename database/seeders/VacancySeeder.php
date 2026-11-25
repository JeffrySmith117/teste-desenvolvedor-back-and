<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    public function run(): void
    {
        Vacancy::factory()->count(40)->create();
        Vacancy::factory()->count(5)->paused()->create();
    }
}
