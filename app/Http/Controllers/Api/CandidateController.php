<?php

namespace App\Http\Controllers\Api;

use App\Filters\CandidateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\Candidate\StoreCandidateRequest;
use App\Http\Requests\Candidate\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use App\Support\PerPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CandidateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $candidates = Candidate::query()
            ->withCount('vacancies')
            ->filter(new CandidateFilter($request->query()))
            ->paginate(PerPage::resolve($request->query('per_page')))
            ->withQueryString();

        return CandidateResource::collection($candidates);
    }

    public function store(StoreCandidateRequest $request): JsonResponse
    {
        $candidate = Candidate::query()->create($request->validated());

        return (new CandidateResource($candidate->refresh()))->response()->setStatusCode(201);
    }

    public function show(Candidate $candidate): CandidateResource
    {
        return new CandidateResource($candidate->loadCount('vacancies')->load('vacancies'));
    }

    public function update(UpdateCandidateRequest $request, Candidate $candidate): CandidateResource
    {
        $candidate->update($request->validated());

        return new CandidateResource($candidate->refresh());
    }

    public function destroy(Candidate $candidate): Response
    {
        $candidate->delete();

        return response()->noContent();
    }

    public function bulkDestroy(BulkDestroyRequest $request): JsonResponse
    {
        $deleted = Candidate::query()->whereKey($request->ids())->delete();

        return response()->json(['deleted' => $deleted]);
    }
}