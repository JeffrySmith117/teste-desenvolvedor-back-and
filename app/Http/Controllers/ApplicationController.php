<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Http\Requests\ApplicationRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function store(ApplicationRequest $request): RedirectResponse
    {
        $candidate = Candidate::query()->findOrFail($request->integer('candidate_id'));
        $vacancy = Vacancy::query()->findOrFail($request->integer('vacancy_id'));

        try {
            $this->applications->apply($candidate, $vacancy);
        } catch (ApplicationException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Inscrição realizada com sucesso.');
    }

    public function destroy(Candidate $candidate, Vacancy $vacancy): RedirectResponse
    {
        try {
            $this->applications->withdraw($candidate, $vacancy);
        } catch (ApplicationException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Inscrição cancelada.');
    }
}
