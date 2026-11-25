<?php

namespace App\Http\Controllers;

use App\Enums\ContractType;
use App\Filters\VacancyFilter;
use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\Vacancy\StoreVacancyRequest;
use App\Http\Requests\Vacancy\UpdateVacancyRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Support\PerPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacancyController extends Controller
{
    public function index(Request $request): View
    {
        $vacancies = Vacancy::query()
            ->withCount('candidates')
            ->filter(new VacancyFilter($request->query()))
            ->paginate(PerPage::resolve($request->query('per_page')))
            ->withQueryString();

        return view('vacancies.index', [
            'vacancies' => $vacancies,
            'contractTypes' => ContractType::options(),
        ]);
    }

    public function create(): View
    {
        return view('vacancies.create', [
            'vacancy' => new Vacancy,
            'contractTypes' => ContractType::options(),
        ]);
    }

    public function store(StoreVacancyRequest $request): RedirectResponse
    {
        $vacancy = Vacancy::query()->create($request->validated());

        return redirect()
            ->route('vacancies.show', $vacancy)
            ->with('success', 'Vaga criada com sucesso.');
    }

    public function show(Vacancy $vacancy): View
    {
        $vacancy->loadCount('candidates');

        return view('vacancies.show', [
            'vacancy' => $vacancy,
            'candidates' => $vacancy->candidates()
                ->orderByPivot('created_at', 'desc')
                ->paginate(PerPage::DEFAULT),
            'availableCandidates' => Candidate::query()
                ->whereDoesntHave('vacancies', fn ($query) => $query->whereKey($vacancy->getKey()))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function edit(Vacancy $vacancy): View
    {
        return view('vacancies.edit', [
            'vacancy' => $vacancy,
            'contractTypes' => ContractType::options(),
        ]);
    }

    public function update(UpdateVacancyRequest $request, Vacancy $vacancy): RedirectResponse
    {
        $vacancy->update($request->validated());

        return redirect()
            ->route('vacancies.show', $vacancy)
            ->with('success', 'Vaga atualizada com sucesso.');
    }

    public function destroy(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->delete();

        return redirect()
            ->route('vacancies.index')
            ->with('success', 'Vaga excluída com sucesso.');
    }

    public function bulkDestroy(BulkDestroyRequest $request): RedirectResponse
    {
        $deleted = Vacancy::query()->whereKey($request->ids())->delete();

        return redirect()
            ->back(fallback: route('vacancies.index'))
            ->with('success', "{$deleted} vaga(s) excluída(s) com sucesso.");
    }

    public function togglePause(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->is_paused ? $vacancy->resume() : $vacancy->pause();

        $message = $vacancy->is_paused
            ? 'Vaga pausada. Novas inscrições estão bloqueadas.'
            : 'Vaga reaberta para inscrições.';

        return redirect()->back(fallback: route('vacancies.show', $vacancy))->with('success', $message);
    }
}
