@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between items-center py-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="h-8 px-3 inline-flex items-center justify-center text-xs font-medium text-zinc-400 dark:text-zinc-600 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md cursor-not-allowed select-none">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="h-8 px-3 inline-flex items-center justify-center text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="h-8 px-3 inline-flex items-center justify-center text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="h-8 px-3 inline-flex items-center justify-center text-xs font-medium text-zinc-400 dark:text-zinc-600 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md cursor-not-allowed select-none">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
