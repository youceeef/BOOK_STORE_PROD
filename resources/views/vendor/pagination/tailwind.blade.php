@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-t border-zinc-200 dark:border-zinc-800">
        {{-- Results Counter --}}
        <div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                {{ __('Showing') }} <span class="font-semibold text-zinc-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span> {{ __('to') }} <span class="font-semibold text-zinc-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span> {{ __('of') }} <span class="font-semibold text-zinc-900 dark:text-white">{{ $paginator->total() }}</span> {{ __('volumes') }}
            </p>
        </div>

        {{-- shadcn/ui Minimal Pagination --}}
        <div class="flex items-center gap-1.5">
            {{-- Previous Page Button --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="h-8 px-2.5 inline-flex items-center justify-center gap-1.5 rounded-md border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-xs font-medium text-zinc-400 dark:text-zinc-600 cursor-not-allowed select-none">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Previous') }}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="h-8 px-2.5 inline-flex items-center justify-center gap-1.5 rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Previous') }}</span>
                </a>
            @endif

            {{-- Numbered Page Links --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="h-8 w-8 flex items-center justify-center text-xs text-zinc-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="h-8 w-8 flex items-center justify-center rounded-md bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-xs font-semibold select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="h-8 w-8 flex items-center justify-center rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
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
                    class="h-8 px-2.5 inline-flex items-center justify-center gap-1.5 rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#12131a] text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    <span class="hidden sm:inline">{{ __('Next') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" class="h-8 px-2.5 inline-flex items-center justify-center gap-1.5 rounded-md border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-xs font-medium text-zinc-400 dark:text-zinc-600 cursor-not-allowed select-none">
                    <span class="hidden sm:inline">{{ __('Next') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
