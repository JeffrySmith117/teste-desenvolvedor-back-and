@extends('layouts.app')

@section('title', $candidate->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $candidate->name }}</h1>
            <p class="muted">Candidato #{{ $candidate->id }}</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('candidates.index') }}" class="btn btn--ghost">Voltar</a>
            <a href="{{ route('candidates.edit', $candidate) }}" class="btn btn--ghost">Editar</a>
            <form method="POST" action="{{ route('candidates.destroy', $candidate) }}" class="inline" data-confirm="Excluir este candidato? As inscrições também serão removidas.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="card">
        <dl class="detail-list">
            <dt>E-mail</dt>
            <dd>{{ $candidate->email }}</dd>

            <dt>Telefone</dt>
            <dd>{{ $candidate->phone ?? '—' }}</dd>

            <dt>Data de nascimento</dt>
            <dd>{{ $candidate->birth_date?->format('d/m/Y') ?? '—' }}</dd>

            <dt>LinkedIn</dt>
            <dd>
                @if ($candidate->linkedin_url)
                    <a href="{{ $candidate->linkedin_url }}" target="_blank" rel="noopener">{{ $candidate->linkedin_url }}</a>
                @else
                    —
                @endif
            </dd>

            <dt>Resumo profissional</dt>
            <dd class="pre-line">{{ $candidate->summary ?? '—' }}</dd>
        </dl>
    </div>

    <div class="card">
        <h2>Inscrever em uma vaga</h2>

        @if ($openVacancies->isEmpty())
            <p class="muted">Não há vagas abertas disponíveis para este candidato.</p>
        @else
            <form method="POST" action="{{ route('applications.store') }}" class="inline-form">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                <select name="vacancy_id" required>
                    <option value="">Selecione uma vaga aberta...</option>
                    @foreach ($openVacancies as $vacancy)
                        <option value="{{ $vacancy->id }}">{{ $vacancy->title }} - {{ $vacancy->company }} ({{ $vacancy->contract_type->label() }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn--primary">Inscrever</button>
            </form>
            @include('partials.field-error', ['field' => 'vacancy_id'])
        @endif
    </div>

    <div class="card">
        <h2>Inscrições ({{ $applications->count() }})</h2>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vaga</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Status da vaga</th>
                        <th>Inscrito em</th>
                        <th class="table__actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $vacancy)
                        <tr>
                            <td><a href="{{ route('vacancies.show', $vacancy) }}">{{ $vacancy->title }}</a></td>
                            <td>{{ $vacancy->company }}</td>
                            <td><span class="badge badge--{{ $vacancy->contract_type->value }}">{{ $vacancy->contract_type->label() }}</span></td>
                            <td>
                                @if ($vacancy->is_paused)
                                    <span class="status status--paused">Pausada</span>
                                @else
                                    <span class="status status--open">Aberta</span>
                                @endif
                            </td>
                            <td>{{ $vacancy->pivot->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="table__actions">
                                <form method="POST" action="{{ route('applications.destroy', [$candidate, $vacancy]) }}" class="inline" data-confirm="Cancelar a inscrição nesta vaga?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger btn--sm">Cancelar inscrição</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table__empty">Este candidato ainda não se inscreveu em nenhuma vaga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection