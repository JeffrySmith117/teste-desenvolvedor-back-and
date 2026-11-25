@extends('layouts.app')

@section('title', 'Editar vaga')

@section('content')
    <div class="page-header">
        <h1>Editar vaga #{{ $vacancy->id }}</h1>
        <a href="{{ route('vacancies.show', $vacancy) }}" class="btn btn--ghost">Voltar</a>
    </div>

    <form method="POST" action="{{ route('vacancies.update', $vacancy) }}" class="card" novalidate>
        @csrf
        @method('PUT')
        @include('vacancies._form')
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Salvar alterações</button>
        </div>
    </form>
@endsection
