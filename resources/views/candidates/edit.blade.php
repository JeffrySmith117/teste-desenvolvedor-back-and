@extends('layouts.app')

@section('title', 'Editar candidato')

@section('content')
    <div class="page-header">
        <h1>Editar candidato #{{ $candidate->id }}</h1>
        <a href="{{ route('candidates.show', $candidate) }}" class="btn btn--ghost">Voltar</a>
    </div>

    <form method="POST" action="{{ route('candidates.update', $candidate) }}" class="card" novalidate>
        @csrf
        @method('PUT')
        @include('candidates._form')
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Salvar alterações</button>
        </div>
    </form>
@endsection