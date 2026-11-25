<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('company', 150);
            $table->string('location', 150)->nullable();
            $table->string('contract_type', 20)->index();
            $table->decimal('salary', 10, 2)->nullable();
            $table->text('description');
            $table->boolean('is_paused')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
