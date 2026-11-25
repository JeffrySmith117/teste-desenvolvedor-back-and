@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
    <div class="auth">
        <form method="POST" action="{{ route('register') }}" class="card auth__card" novalidate>
            @csrf
            <h1>Criar conta</h1>

            <div class="field">
                <label for="name">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       @class(['is-invalid' => $errors->has('name')])>
                @include('partials.field-error', ['field' => 'name'])
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       @class(['is-invalid' => $errors->has('email')])>
                @include('partials.field-error', ['field' => 'email'])
            </div>

            <div class="field">
                <label for="password">Senha (mínimo 8 caracteres)</label>
                <input type="password" id="password" name="password" required
                       @class(['is-invalid' => $errors->has('password')])>
                @include('partials.field-error', ['field' => 'password'])
            </div>

            <div class="field">
                <label for="password_confirmation">Confirme a senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Cadastrar</button>

            <p class="muted auth__footer">
                Já tem conta? <a href="{{ route('login') }}">Entrar</a>
            </p>
        </form>
    </div>
@endsection