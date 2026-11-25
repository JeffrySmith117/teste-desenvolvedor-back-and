{{-- Cabeçalho de coluna ordenável. Parâmetros: $column, $label --}}
@php
    $isCurrent = request('sort') === $column;
    $nextDirection = $isCurrent && request('direction') === 'asc' ? 'desc' : 'asc';
@endphp
<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]) }}"
   @class(['sort-link', 'is-active' => $isCurrent])>
    {{ $label }}
    @if ($isCurrent)
        <span aria-hidden="true">{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
    @endif
</a>
