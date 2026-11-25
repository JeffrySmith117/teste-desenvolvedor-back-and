@extends('layouts.app')

@section('title', 'Vagas')

@section('content')
    <div class="page-header">
        <h1>Vagas</h1>
        <a href="{{ route('vacancies.create') }}" class="btn btn--primary">+ Nova vaga</a>
    </div>

    <details class="card filters" @if (count(request()->except(['page', 'per_page', 'sort', 'direction']))) open @endif>
        <summary>Filtros</summary>
        <form method="GET" action="{{ route('vacancies.index') }}" class="filters__grid">
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            <div class="field">
                <label for="f-id">ID</label>
                <input type="number" min="1" id="f-id" name="id" value="{{ request('id') }}">
            </div>
            <div class="field">
                <label for="f-title">Título</label>
                <input type="text" id="f-title" name="title" value="{{ request('title') }}">
            </div>
            <div class="field">
                <label for="f-company">Empresa</label>
                <input type="text" id="f-company" name="company" value="{{ request('company') }}">
            </div>
            <div class="field">
                <label for="f-location">Local</label>
                <input type="text" id="f-location" name="location" value="{{ request('location') }}">
            </div>
            <div class="field">
                <label for="f-contract">Tipo</label>
                <select id="f-contract" name="contract_type">
                    <option value="">Todos</option>
                    @foreach ($contractTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('contract_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="f-status">Status</label>
                <select id="f-status" name="status">
                    <option value="">Todos</option>
                    <option value="open" @selected(request('status') === 'open')>Aberta</option>
                    <option value="paused" @selected(request('status') === 'paused')>Pausada</option>
                </select>
            </div>
            <div class="field">
                <label for="f-salary-min">Salário mínimo</label>
                <input type="number" step="0.01" min="0" id="f-salary-min" name="salary_min" value="{{ request('salary_min') }}">
            </div>
            <div class="field">
                <label for="f-salary-max">Salário máximo</label>
                <input type="number" step="0.01" min="0" id="f-salary-max" name="salary_max" value="{{ request('salary_max') }}">
            </div>
            <div class="field">
                <label for="f-description">Descrição contém</label>
                <input type="text" id="f-description" name="description" value="{{ request('description') }}">
            </div>
            <div class="field">
                <label for="f-from">Criada a partir de</label>
                <input type="date" id="f-from" name="created_from" value="{{ request('created_from') }}">
            </div>
            <div class="field">
                <label for="f-to">Criada até</label>
                <input type="date" id="f-to" name="created_to" value="{{ request('created_to') }}">
            </div>
            <div class="filters__actions">
                <button type="submit" class="btn btn--primary">Filtrar</button>
                <a href="{{ route('vacancies.index') }}" class="btn btn--ghost">Limpar</a>
            </div>
        </form>
    </details>

    <form id="bulk-form" method="POST" action="{{ route('vacancies.bulk-destroy') }}" data-bulk-form data-confirm="Excluir as vagas selecionadas?">
        @csrf
        @method('DELETE')
    </form>

    <div class="card">
        <div class="toolbar">
            <button type="submit" form="bulk-form" class="btn btn--danger btn--sm" data-bulk-submit disabled>
                Excluir selecionadas (<span data-bulk-count>0</span>)
            </button>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th class="table__check"><input type="checkbox" data-check-all aria-label="Selecionar todas"></th>
                        <th>@include('partials.sort-link', ['column' => 'id', 'label' => 'ID'])</th>
                        <th>@include('partials.sort-link', ['column' => 'title', 'label' => 'Título'])</th>
                        <th>@include('partials.sort-link', ['column' => 'company', 'label' => 'Empresa'])</th>
                        <th>@include('partials.sort-link', ['column' => 'location', 'label' => 'Local'])</th>
                        <th>@include('partials.sort-link', ['column' => 'contract_type', 'label' => 'Tipo'])</th>
                        <th>@include('partials.sort-link', ['column' => 'salary', 'label' => 'Salário'])</th>
                        <th>@include('partials.sort-link', ['column' => 'is_paused', 'label' => 'Status'])</th>
                        <th>Inscritos</th>
                        <th class="table__actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vacancies as $vacancy)
                        <tr>
                            <td class="table__check">
                                <input type="checkbox" name="ids[]" value="{{ $vacancy->id }}" form="bulk-form" data-check-item aria-label="Selecionar vaga {{ $vacancy->id }}">
                            </td>
                            <td>{{ $vacancy->id }}</td>
                            <td><a href="{{ route('vacancies.show', $vacancy) }}">{{ $vacancy->title }}</a></td>
                            <td>{{ $vacancy->company }}</td>
                            <td>{{ $vacancy->location ?? '—' }}</td>
                            <td><span class="badge badge--{{ $vacancy->contract_type->value }}">{{ $vacancy->contract_type->label() }}</span></td>
                            <td>{{ $vacancy->salary ? 'R$ '.number_format((float) $vacancy->salary, 2, ',', '.') : 'A combinar' }}</td>
                            <td>
                                @if ($vacancy->is_paused)
                                    <span class="status status--paused">Pausada</span>
                                @else
                                    <span class="status status--open">Aberta</span>
                                @endif
                            </td>
                            <td>{{ $vacancy->candidates_count }}</td>
                            <td class="table__actions">
                                <a href="{{ route('vacancies.edit', $vacancy) }}" class="btn btn--ghost btn--sm">Editar</a>
                                <form method="POST" action="{{ route('vacancies.toggle-pause', $vacancy) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn--ghost btn--sm">{{ $vacancy->is_paused ? 'Reabrir' : 'Pausar' }}</button>
                                </form>
                                <form method="POST" action="{{ route('vacancies.destroy', $vacancy) }}" class="inline" data-confirm="Excluir a vaga &quot;{{ $vacancy->title }}&quot;?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger btn--sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="table__empty">Nenhuma vaga encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.list-footer', ['paginator' => $vacancies])
    </div>
@endsection
