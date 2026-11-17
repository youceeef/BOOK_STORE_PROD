@props(['order'])

@php
    $step = $order->getStepIndex();
    $isCancelled = ($step === -1);

    $stages = [
        1 => [
            'name' => 'Order Confirmed',
            'desc' => 'Payment received & volumes queued',
            'date' => $order->created_at?->format('M d, Y · H:i'),
        ],
        2 => [
            'name' => 'Archival Preparation',
            'desc' => 'Volumes gathered & quality inspected',
            'date' => ($step >= 2) ? ($order->updated_at?->format('M d, Y · H:i')) : null,
        ],
        3 => [
            'name' => 'In Transit',
            'desc' => $order->carrier ? ($order->carrier . ' tracking dispatched') : 'Dispatched with carrier',
            'date' => $order->shipped_at?->format('M d, Y · H:i'),
        ],
        4 => [
            'name' => 'Delivered',
            'desc' => 'Signed & delivered to doorstep',
            'date' => $order->delivered_at?->format('M d, Y · H:i'),
        ],
    ];
@endphp

<div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800/80 pb-4">
        <div>
            <span class="text-[11px] font-semibold text-zinc-400 block mb-0.5">Fulfillment Tracker</span>
            <h3 class="font-serif text-lg font-bold text-zinc-900 dark:text-white">
                @if ($isCancelled)
                    Order Cancelled
                @elseif ($step === 4)
                    Delivered & Completed
                @elseif ($step === 3)
                    In Transit with {{ $order->carrier ?? 'Carrier' }}
                @elseif ($step === 2)
                    Preparing in Bindery
                @else
                    Order Received & Confirmed
                @endif
            </h3>
        </div>

        @if ($order->estimated_delivery_at && !$isCancelled && $step < 4)
            <div class="text-left sm:text-right">
                <span class="text-[11px] text-zinc-400 block">Estimated Delivery</span>
                <span class="text-xs font-semibold text-zinc-900 dark:text-white">
                    {{ $order->estimated_delivery_at->format('l, M d, Y') }}
                </span>
            </div>
        @endif
    </div>

    @if ($isCancelled)
        <div class="rounded-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 text-xs text-zinc-600 dark:text-zinc-400 flex items-start gap-3">
            <span class="w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 flex items-center justify-center font-bold text-xs shrink-0">✕</span>
            <div>
                <p class="font-semibold text-zinc-900 dark:text-white">Order Voided / Cancelled</p>
                <p class="mt-0.5">This transaction has been cancelled. Any hold on your payment method will be released according to your bank's schedule.</p>
                @if ($order->fulfillment_notes)
                    <p class="mt-1 text-[11px] text-zinc-500 font-mono">Note: {{ $order->fulfillment_notes }}</p>
                @endif
            </div>
        </div>
    @else
        {{-- Responsive Timeline Track --}}
        <div class="relative">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                @foreach ($stages as $index => $stage)
                    @php
                        $isCompleted = ($step > $index || ($step === 4 && $index === 4));
                        $isCurrent = ($step === $index);
                        $isPending = ($step < $index);
                    @endphp

                    <div class="flex md:flex-col items-start gap-3 md:gap-2.5 relative">
                        {{-- Connector Line (Desktop) --}}
                        @if ($index < 4)
                            <div class="hidden md:block absolute top-3.5 left-7 right-[-50%] h-[2px] z-0 {{ $step > $index ? 'bg-zinc-900 dark:bg-white' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>
                        @endif

                        {{-- Step Circle Marker --}}
                        <div class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0 transition-colors
                            {{ $isCompleted ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : '' }}
                            {{ $isCurrent ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 ring-4 ring-zinc-100 dark:ring-zinc-800' : '' }}
                            {{ $isPending ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700' : '' }}">
                            @if ($isCompleted)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span>{{ $index }}</span>
                            @endif
                        </div>

                        {{-- Step Narrative Details --}}
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold {{ $isPending ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-900 dark:text-white' }}">
                                {{ $stage['name'] }}
                            </p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug mt-0.5">
                                {{ $stage['desc'] }}
                            </p>
                            @if ($stage['date'] && !$isPending)
                                <p class="text-[10px] text-zinc-400 font-mono mt-1">
                                    {{ $stage['date'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Logistics & Tracking Strip (Visible when Carrier or Tracking Number exists) --}}
        @if ($order->tracking_number || $order->carrier)
            <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 p-4 text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div class="w-8 h-8 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] text-zinc-400 block">{{ $order->carrier ?? 'Carrier Dispatched' }}</span>
                            <div class="flex items-center space-x-2 rtl:space-x-reverse" x-data="{ copied: false }">
                                <span class="font-mono font-medium text-zinc-900 dark:text-white">{{ $order->tracking_number ?? 'Pending Carrier Upload' }}</span>
                                @if ($order->tracking_number)
                                    <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $order->tracking_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="text-[10px] text-zinc-500 hover:text-zinc-900 dark:hover:text-white px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 transition"
                                        :class="{ 'text-emerald-600 dark:text-emerald-400 border-emerald-300': copied }">
                                        <span x-show="!copied">Copy</span>
                                        <span x-show="copied" style="display: none;">✓ Copied</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-semibold border border-zinc-300 dark:border-zinc-700 hover:bg-white dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 transition shrink-0">
                            <span>Track Package</span>
                            <svg class="w-3 h-3 text-zinc-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                </div>

                @if ($order->fulfillment_notes)
                    <p class="mt-3 pt-3 border-t border-zinc-200/60 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-[11px]">
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">Carrier Notes:</span> {{ $order->fulfillment_notes }}
                    </p>
                @endif
            </div>
        @endif
    @endif
</div>
