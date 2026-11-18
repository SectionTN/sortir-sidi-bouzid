@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="mt-12 flex items-center justify-between gap-4" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-semibold text-stone-400 ring-1 ring-ink/10">
                <x-icon.arrow-left class="fill-current" />
                Précédent
            </span>
        @else
            <x-button :href="$paginator->previousPageUrl()" variant="secondary" rel="prev">
                <x-icon.arrow-left class="fill-current" />
                Précédent
            </x-button>
        @endif

        <p class="text-sm text-stone-600">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</p>

        @if ($paginator->hasMorePages())
            <x-button :href="$paginator->nextPageUrl()" variant="secondary" rel="next">
                Suivant
                <x-icon.arrow-right class="fill-current" />
            </x-button>
        @else
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-semibold text-stone-400 ring-1 ring-ink/10">
                Suivant
                <x-icon.arrow-right class="fill-current" />
            </span>
        @endif
    </nav>
@endif
