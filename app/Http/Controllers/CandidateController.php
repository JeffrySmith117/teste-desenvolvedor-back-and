<?php

namespace App\Http\Controllers;

use App\Filters\CandidateFilter;
use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\Candidate\StoreCandidateRequest;
use App\Http\Requests\Candidate\UpdateCandidateRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Support\PerPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(Request $request): View
    {
        $candidates = Candidate::query()
            ->withCount('vacancies')
            ->filter(new CandidateFilter($request->query()))
            ->paginate(PerPage::resolve($request->query('per_page')))
            ->withQueryString();

        return view('candidates.index', [
            'candidates' => $candidates,
            'vacancies' => Vacancy::query()->orderBy('title')->get(['id', 'title', 'company']),
        ]);
    }

    public function create(): View
    {
        return view('candidates.create', ['candidate' => new Candidate]);
    }

    public function store(StoreCandidateRequest $request): RedirectResponse
    {
        $candidate = Candidate::query()->create($request->validated());

        return redirect()
            ->route('candidates.show', $candidate)
            ->with('success', 'Candidato(a) cadastrado(a) com sucesso.');
    }

    public function show(Candidate $candidate): View
    {
        return view('candidates.show', [
            'candidate' => $candidate,
            'applications' => $candidate->vacancies()->orderByPivot('created_at', 'desc')->get(),
            'openVacancies' => Vacancy::query()
                ->open()
                ->whereDoesntHave('candidates', fn ($query) => $query->whereKey($candidate->getKey()))
                ->orderBy('title')
                ->get(['id', 'title', 'company', 'contract_type']),
        ]);
    }

    public function edit(Candidate $candidate): View
    {
        return view('candidates.edit', ['candidate' => $candidate]);
    }

    public function update(UpdateCandidateRequest $request, Candidate $candidate): RedirectResponse
    {
        $candidate->update($request->validated());

        return redirect()
            ->route('candidates.show', $candidate)
            ->with('success', 'Candidato(a) atualizado(a) com sucesso.');
    }

    public function destroy(Candidate $candidate): RedirectResponse
    {
        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato(a) excluído(a) com sucesso.');
    }

    public function bulkDestroy(BulkDestroyRequest $request): RedirectResponse
    {
        $deleted = Candidate::query()->whereKey($request->ids())->delete();

        return redirect()
            ->back(fallback: route('candidates.index'))
            ->with('success', "{$deleted} candidato(s) excluído(s) com sucesso.");
    }
}
