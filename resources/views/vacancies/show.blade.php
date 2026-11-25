@extends('layouts.app')

@section('title', $vacancy->title)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $vacancy->title }}</h1>
            <p class="muted">{{ $vacancy->company }} · Vaga #{{ $vacancy->id }}</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('vacancies.index') }}" class="btn btn--ghost">Voltar</a>
            <a href="{{ route('vacancies.edit', $vacancy) }}" class="btn btn--ghost">Editar</a>
            <form method="POST" action="{{ route('vacancies.toggle-pause', $vacancy) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn--ghost">{{ $vacancy->is_paused ? 'Reabrir vaga' : 'Pausar vaga' }}</button>
            </form>
            <form method="POST" action="{{ route('vacancies.destroy', $vacancy) }}" class="inline" data-confirm="Excluir esta vaga? As inscrições também serão removidas.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="card">
        <dl class="detail-list">
            <dt>Status</dt>
            <dd>
                @if ($vacancy->is_paused)
                    <span class="status status--paused">Pausada (não aceita inscrições)</span>
                @else
                    <span class="status status--open">Aberta</span>
                @endif
            </dd>

            <dt>Tipo de contratação</dt>
            <dd><span class="badge badge--{{ $vacancy->contract_type->value }}">{{ $vacancy->contract_type->label() }}</span></dd>

            <dt>Local</dt>
            <dd>{{ $vacancy->location ?? '—' }}</dd>

            <dt>Salário</dt>
            <dd>{{ $vacancy->salary ? 'R$ '.number_format((float) $vacancy->salary, 2, ',', '.') : 'A combinar' }}</dd>

            <dt>Criada em</dt>
            <dd>{{ $vacancy->created_at->format('d/m/Y H:i') }}</dd>

            <dt>Descrição</dt>
            <dd class="pre-line">{{ $vacancy->description }}</dd>
        </dl>
    </div>

    <div class="card">
        <h2>Inscrever candidato</h2>

        @if ($vacancy->is_paused)
            <p class="muted">Esta vaga está pausada. Reabra a vaga para permitir novas inscrições.</p>
        @elseif ($availableCandidates->isEmpty())
            <p class="muted">Todos os candidatos cadastrados já estão inscritos nesta vaga.</p>
        @else
            <form method="POST" action="{{ route('applications.store') }}" class="inline-form">
                @csrf
                <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">
                <select name="candidate_id" required>
                    <option value="">Selecione um candidato...</option>
                    @foreach ($availableCandidates as $candidate)
                        <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn--primary">Inscrever</button>
            </form>
            @include('partials.field-error', ['field' => 'candidate_id'])
        @endif
    </div>

    <div class="card">
        <h2>Candidatos inscritos ({{ $vacancy->candidates_count }})</h2>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Inscrito em</th>
                        <th class="table__actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($candidates as $candidate)
                        <tr>
                            <td><a href="{{ route('candidates.show', $candidate) }}">{{ $candidate->name }}</a></td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->phone ?? '—' }}</td>
                            <td>{{ $candidate->pivot->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="table__actions">
                                <form method="POST" action="{{ route('applications.destroy', [$candidate, $vacancy]) }}" class="inline" data-confirm="Cancelar a inscrição de {{ $candidate->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger btn--sm">Cancelar inscrição</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table__empty">Nenhum candidato inscrito ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $candidates->links() }}
    </div>
@endsection