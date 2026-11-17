{{-- resources/views/components/toast.blade.php --}}
<div x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            const newToast = {
                id: id,
                title: toast.title || 'Notification',
                message: toast.message || '',
                type: toast.type || 'success', // success, error, info, warning
                actionUrl: toast.actionUrl || null,
                actionLabel: toast.actionLabel || null,
                timeout: toast.timeout || 4000
            };
            this.toasts.push(newToast);
            setTimeout(() => {
                this.remove(id);
            }, newToast.timeout);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @notify.window="add($event.detail)"
    class="fixed bottom-5 right-5 rtl:right-auto rtl:left-5 z-50 flex flex-col space-y-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="pointer-events-auto bg-white/95 dark:bg-[#1a1a2e]/95 backdrop-blur-md border border-gray-200/90 dark:border-navy-800 shadow-xl rounded-2xl p-4 flex items-start space-x-3.5 rtl:space-x-reverse relative overflow-hidden group">
            
            {{-- Accent Icon Indicator --}}
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <div class="w-7 h-7 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200/60 dark:border-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'error'">
                    <div class="w-7 h-7 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200/60 dark:border-rose-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'info' || toast.type === 'warning'">
                    <div class="w-7 h-7 rounded-full bg-[#1a1a2e] text-gold-400 flex items-center justify-center border border-gold-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </template>
            </div>

            {{-- Body Content --}}
            <div class="flex-1 min-w-0 pr-2 rtl:pr-0 rtl:pl-2">
                <h4 class="font-serif font-bold text-sm text-gray-900 dark:text-gray-100 leading-snug" x-text="toast.title"></h4>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed" x-text="toast.message"></p>

                {{-- Action Link / Button if specified --}}
                <template x-if="toast.actionUrl">
                    <a :href="toast.actionUrl" class="inline-flex items-center text-xs font-semibold text-gold-600 dark:text-gold-400 hover:text-gold-700 mt-2 hover:underline">
                        <span x-text="toast.actionLabel || 'View Details'"></span>
                        <span class="ml-1 rtl:ml-0 rtl:mr-1">→</span>
                    </a>
                </template>
            </div>

            {{-- Close Button --}}
            <button type="button" @click="remove(toast.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 -mr-1 rtl:-mr-0 rtl:-ml-1 -mt-1 rounded-lg">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>