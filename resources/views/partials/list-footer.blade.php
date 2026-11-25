{{-- Rodapé das listagens: total, seletor de itens por página e paginação. --}}
<div class="list-footer">
    <p class="muted">
        @if ($paginator->total() > 0)
            Exibindo {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        @else
            Nenhum registro
        @endif
    </p>

    <form method="GET" class="per-page" data-auto-submit>
        @foreach (request()->except(['per_page', 'page']) as $key => $value)
            @if (is_scalar($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <label for="per_page">Itens por página</label>
        <select name="per_page" id="per_page">
            @foreach (\App\Support\PerPage::OPTIONS as $option)
                <option value="{{ $option }}" @selected($paginator->perPage() === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </form>

    {{ $paginator->links() }}
</div>
