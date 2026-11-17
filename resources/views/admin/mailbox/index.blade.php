<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 block mb-0.5">Communications & Mail</span>
                <h1 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">
                    Transactional Mailbox Inspector
                </h1>
            </div>
            @if(count($emails) > 0)
                <form action="{{ route('admin.mailbox.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-md text-xs font-medium text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition">
                        Clear Mailbox Log
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl mx-auto">
        {{-- Overview Banner --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 text-xs text-zinc-600 dark:text-zinc-400">
            <div class="flex items-start space-x-3 rtl:space-x-reverse">
                <div class="w-8 h-8 rounded-md bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-zinc-700 dark:text-zinc-300 font-bold">
                    ✉
                </div>
                <div>
                    <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">Resilient Failover Mailbox Engine</h2>
                    <p class="mt-0.5">
                        The bookstore utilizes a <strong>failover transport</strong>: outgoing emails attempt real SMTP transmission first, and gracefully log upon network timeouts without crashing. In addition, all recent auth tokens and links are captured below for seamless live testing.
                    </p>
                </div>
            </div>
        </div>

        {{-- Emails Table --}}
        <div class="bg-white dark:bg-[#12131a] rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-4 sm:px-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <h2 class="font-serif font-bold text-sm text-zinc-900 dark:text-white">Recent Outgoing Communications</h2>
                <span class="text-xs text-zinc-400">{{ count($emails) }} {{ Str::plural('email', count($emails)) }} logged</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400 font-medium">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Recipient</th>
                            <th class="px-5 py-3 font-semibold">Subject</th>
                            <th class="px-5 py-3 font-semibold">Type</th>
                            <th class="px-5 py-3 font-semibold">Sent At</th>
                            <th class="px-5 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-zinc-700 dark:text-zinc-300">
                        @forelse ($emails as $email)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-white">
                                    {{ $email['recipient'] }}
                                </td>
                                <td class="px-5 py-3.5">
                                    {{ $email['subject'] }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $typeBadge = match($email['type']) {
                                            'verify_email'   => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                            'password_reset' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                            default          => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $typeBadge }}">
                                        {{ str_replace('_', ' ', ucfirst($email['type'])) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-zinc-500 font-mono text-[11px]">
                                    {{ $email['created_at'] }}
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2 rtl:space-x-reverse" x-data="{ copied: false }">
                                    <a href="{{ $email['url'] }}" target="_blank"
                                        class="inline-flex items-center space-x-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition shadow-xs">
                                        <span>Open Link</span>
                                        <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $email['url'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="px-2 py-1 rounded text-[11px] font-medium border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                        :class="{ 'text-emerald-600 border-emerald-300': copied }">
                                        <span x-show="!copied">Copy</span>
                                        <span x-show="copied" style="display: none;">✓ Copied</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-xs text-zinc-400">
                                    No outgoing emails captured in this session yet. Trigger an email verification or password reset to inspect it live.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
