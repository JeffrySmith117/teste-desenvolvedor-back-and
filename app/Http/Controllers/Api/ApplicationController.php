<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\VacancyResource;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Erros de regra de negócio (vaga pausada, inscrição duplicada) são
 * convertidos em 422 pelo handler configurado em bootstrap/app.php.
 */
class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function store(ApplicationRequest $request): JsonResponse
    {
        $candidate = Candidate::query()->findOrFail($request->integer('candidate_id'));
        $vacancy = Vacancy::query()->findOrFail($request->integer('vacancy_id'));

        $this->applications->apply($candidate, $vacancy);

        return response()->json([
            'message' => 'Inscrição realizada com sucesso.',
            'candidate_id' => $candidate->id,
            'vacancy' => new VacancyResource($vacancy),
        ], 201);
    }

    public function destroy(Candidate $candidate, Vacancy $vacancy): Response
    {
        $this->applications->withdraw($candidate, $vacancy);

        return response()->noContent();
    }
}