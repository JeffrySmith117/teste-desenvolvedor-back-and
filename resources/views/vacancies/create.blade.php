@extends('layouts.app')

@section('title', 'Nova vaga')

@section('content')
    <div class="page-header">
        <h1>Nova vaga</h1>
        <a href="{{ route('vacancies.index') }}" class="btn btn--ghost">Voltar</a>
    </div>

    <form method="POST" action="{{ route('vacancies.store') }}" class="card" novalidate>
        @csrf
        @include('vacancies._form')
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Salvar</button>
        </div>
    </form>
@endsection
