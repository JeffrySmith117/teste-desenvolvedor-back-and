<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vagas') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @auth
        <header class="topbar">
            <div class="container topbar__inner">
                <a href="{{ route('vacancies.index') }}" class="brand">Banco de Vagas</a>
                <nav class="nav">
                    <a href="{{ route('vacancies.index') }}" @class(['nav__link', 'is-active' => request()->routeIs('vacancies.*')])>Vagas</a>
                    <a href="{{ route('candidates.index') }}" @class(['nav__link', 'is-active' => request()->routeIs('candidates.*')])>Candidatos</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="topbar__user">
                    @csrf
                    <span>{{ auth()->user()->name }}</span>
                    <button type="submit" class="btn btn--ghost btn--sm">Sair</button>
                </form>
            </div>
        </header>
    @endauth

    <main class="container main">
        @include('partials.flash')
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
