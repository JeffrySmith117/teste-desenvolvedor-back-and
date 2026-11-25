@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
    <div class="auth">
        <form method="POST" action="{{ route('login') }}" class="card auth__card" novalidate>
            @csrf
            <h1>Banco de Vagas</h1>
            <p class="muted">Entre para gerenciar vagas e candidatos.</p>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       @class(['is-invalid' => $errors->has('email')])>
                @include('partials.field-error', ['field' => 'email'])
            </div>

            <div class="field">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required
                       @class(['is-invalid' => $errors->has('password')])>
                @include('partials.field-error', ['field' => 'password'])
            </div>

            <label class="checkbox">
                <input type="checkbox" name="remember" value="1"> Lembrar de mim
            </label>

            <button type="submit" class="btn btn--primary btn--block">Entrar</button>

            <p class="muted auth__footer">
                Não tem conta? <a href="{{ route('register') }}">Cadastre-se</a>
            </p>
        </form>
    </div>
@endsection