<?php

namespace App\Http\Controllers\Api;

use App\Filters\VacancyFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\Vacancy\StoreVacancyRequest;
use App\Http\Requests\Vacancy\UpdateVacancyRequest;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use App\Support\PerPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class VacancyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vacancies = Vacancy::query()
            ->withCount('candidates')
            ->filter(new VacancyFilter($request->query()))
            ->paginate(PerPage::resolve($request->query('per_page')))
            ->withQueryString();

        return VacancyResource::collection($vacancies);
    }

    public function store(StoreVacancyRequest $request): JsonResponse
    {
        $vacancy = Vacancy::query()->create($request->validated());

        return (new VacancyResource($vacancy->refresh()))->response()->setStatusCode(201);
    }

    public function show(Vacancy $vacancy): VacancyResource
    {
        return new VacancyResource($vacancy->loadCount('candidates')->load('candidates'));
    }

    public function update(UpdateVacancyRequest $request, Vacancy $vacancy): VacancyResource
    {
        $vacancy->update($request->validated());

        return new VacancyResource($vacancy->refresh());
    }

    public function destroy(Vacancy $vacancy): Response
    {
        $vacancy->delete();

        return response()->noContent();
    }

    public function bulkDestroy(BulkDestroyRequest $request): JsonResponse
    {
        $deleted = Vacancy::query()->whereKey($request->ids())->delete();

        return response()->json(['deleted' => $deleted]);
    }

    public function togglePause(Vacancy $vacancy): VacancyResource
    {
        $vacancy->is_paused ? $vacancy->resume() : $vacancy->pause();

        return new VacancyResource($vacancy);
    }
}
