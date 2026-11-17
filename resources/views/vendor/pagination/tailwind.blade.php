@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-t border-gray-100">
        {{-- Results Counter --}}
        <div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Showing <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span> volumes
            </p>
        </div>

        {{-- shadcn/ui Pagination Component --}}
        <div class="flex items-center gap-1.5">
            {{-- Previous Page Button --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="h-9 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50/50 text-xs font-medium text-gray-400 cursor-not-allowed select-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="h-9 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 shadow-sm transition active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </a>
            @endif

            {{-- Numbered Page Links --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="h-9 w-9 flex items-center justify-center text-xs font-semibold text-gray-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="h-9 w-9 flex items-center justify-center rounded-lg bg-[#1a1a2e] text-gold-400 border border-[#1a1a2e] text-xs font-bold shadow-sm select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="h-9 w-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 shadow-sm transition active:scale-95"
                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="h-9 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 shadow-sm transition active:scale-95">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" class="h-9 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50/50 text-xs font-medium text-gray-400 cursor-not-allowed select-none">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
