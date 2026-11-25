@extends('layouts.app')

@section('title', 'Novo candidato')

@section('content')
    <div class="page-header">
        <h1>Novo candidato</h1>
        <a href="{{ route('candidates.index') }}" class="btn btn--ghost">Voltar</a>
    </div>

    <form method="POST" action="{{ route('candidates.store') }}" class="card" novalidate>
        @csrf
        @include('candidates._form')
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Salvar</button>
        </div>
    </form>
@endsection