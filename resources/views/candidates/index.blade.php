@extends('layouts.app')

@section('title', 'Candidatos')

@section('content')
    <div class="page-header">
        <h1>Candidatos</h1>
        <a href="{{ route('candidates.create') }}" class="btn btn--primary">+ Novo candidato</a>
    </div>

    <details class="card filters" @if (count(request()->except(['page', 'per_page', 'sort', 'direction']))) open @endif>
        <summary>Filtros</summary>
        <form method="GET" action="{{ route('candidates.index') }}" class="filters__grid">
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            <div class="field">
                <label for="f-id">ID</label>
                <input type="number" min="1" id="f-id" name="id" value="{{ request('id') }}">
            </div>
            <div class="field">
                <label for="f-name">Nome</label>
                <input type="text" id="f-name" name="name" value="{{ request('name') }}">
            </div>
            <div class="field">
                <label for="f-email">E-mail</label>
                <input type="text" id="f-email" name="email" value="{{ request('email') }}">
            </div>
            <div class="field">
                <label for="f-phone">Telefone</label>
                <input type="text" id="f-phone" name="phone" value="{{ request('phone') }}">
            </div>
            <div class="field">
                <label for="f-linkedin">LinkedIn</label>
                <input type="text" id="f-linkedin" name="linkedin_url" value="{{ request('linkedin_url') }}">
            </div>
            <div class="field">
                <label for="f-summary">Resumo contém</label>
                <input type="text" id="f-summary" name="summary" value="{{ request('summary') }}">
            </div>
            <div class="field">
                <label for="f-birth-from">Nascido(a) a partir de</label>
                <input type="date" id="f-birth-from" name="birth_date_from" value="{{ request('birth_date_from') }}">
            </div>
            <div class="field">
                <label for="f-birth-to">Nascido(a) até</label>
                <input type="date" id="f-birth-to" name="birth_date_to" value="{{ request('birth_date_to') }}">
            </div>
            <div class="field">
                <label for="f-created-from">Cadastrado a partir de</label>
                <input type="date" id="f-created-from" name="created_from" value="{{ request('created_from') }}">
            </div>
            <div class="field">
                <label for="f-created-to">Cadastrado até</label>
                <input type="date" id="f-created-to" name="created_to" value="{{ request('created_to') }}">
            </div>
            <div class="field">
                <label for="f-vacancy">Inscrito na vaga</label>
                <select id="f-vacancy" name="vacancy_id">
                    <option value="">Qualquer</option>
                    @foreach ($vacancies as $vacancy)
                        <option value="{{ $vacancy->id }}" @selected((string) request('vacancy_id') === (string) $vacancy->id)>
                            #{{ $vacancy->id }} - {{ $vacancy->title }} ({{ $vacancy->company }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filters__actions">
                <button type="submit" class="btn btn--primary">Filtrar</button>
                <a href="{{ route('candidates.index') }}" class="btn btn--ghost">Limpar</a>
            </div>
        </form>
    </details>

    <form id="bulk-form" method="POST" action="{{ route('candidates.bulk-destroy') }}" data-confirm="Excluir os candidatos selecionados?">
        @csrf
        @method('DELETE')
    </form>

    <div class="card">
        <div class="toolbar">
            <button type="submit" form="bulk-form" class="btn btn--danger btn--sm" data-bulk-submit disabled>
                Excluir selecionados (<span data-bulk-count>0</span>)
            </button>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th class="table__check"><input type="checkbox" data-check-all aria-label="Selecionar todos"></th>
                        <th>@include('partials.sort-link', ['column' => 'id', 'label' => 'ID'])</th>
                        <th>@include('partials.sort-link', ['column' => 'name', 'label' => 'Nome'])</th>
                        <th>@include('partials.sort-link', ['column' => 'email', 'label' => 'E-mail'])</th>
                        <th>@include('partials.sort-link', ['column' => 'phone', 'label' => 'Telefone'])</th>
                        <th>@include('partials.sort-link', ['column' => 'birth_date', 'label' => 'Nascimento'])</th>
                        <th>Inscrições</th>
                        <th class="table__actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($candidates as $candidate)
                        <tr>
                            <td class="table__check">
                                <input type="checkbox" name="ids[]" value="{{ $candidate->id }}" form="bulk-form" data-check-item aria-label="Selecionar candidato {{ $candidate->id }}">
                            </td>
                            <td>{{ $candidate->id }}</td>
                            <td><a href="{{ route('candidates.show', $candidate) }}">{{ $candidate->name }}</a></td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->phone ?? '—' }}</td>
                            <td>{{ $candidate->birth_date?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $candidate->vacancies_count }}</td>
                            <td class="table__actions">
                                <a href="{{ route('candidates.edit', $candidate) }}" class="btn btn--ghost btn--sm">Editar</a>
                                <form method="POST" action="{{ route('candidates.destroy', $candidate) }}" class="inline" data-confirm="Excluir {{ $candidate->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger btn--sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table__empty">Nenhum candidato encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.list-footer', ['paginator' => $candidates])
    </div>
@endsection