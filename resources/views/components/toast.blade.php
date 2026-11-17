{{-- resources/views/components/toast.blade.php --}}
<div x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            const newToast = {
                id: id,
                title: toast.title || '',
                message: toast.message || '',
                type: toast.type || 'info', // success, error, info
                actionUrl: toast.actionUrl || null,
                actionLabel: toast.actionLabel || null,
                timeout: toast.timeout || 3500
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
    class="fixed bottom-5 right-5 rtl:right-auto rtl:left-5 z-50 flex flex-col space-y-2 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-150 transform"
             x-transition:enter-start="opacity-0 translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-100 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-98"
             class="pointer-events-auto bg-white dark:bg-[#12131a] border border-zinc-200 dark:border-zinc-800 shadow-md rounded-lg p-3.5 flex items-start space-x-3 rtl:space-x-reverse">
            
            {{-- Clean Status Indicator --}}
            <div class="flex-shrink-0 mt-0.5 text-xs font-bold">
                <template x-if="toast.type === 'success'">
                    <span class="text-emerald-600 dark:text-emerald-400">✓</span>
                </template>
                <template x-if="toast.type === 'error'">
                    <span class="text-rose-600 dark:text-rose-400">✕</span>
                </template>
                <template x-if="toast.type === 'info' || toast.type === 'warning'">
                    <span class="text-zinc-600 dark:text-zinc-400">•</span>
                </template>
            </div>

            {{-- Message Body --}}
            <div class="flex-1 min-w-0 pr-1 rtl:pr-0 rtl:pl-1">
                <p class="text-xs font-medium text-zinc-900 dark:text-zinc-100 leading-snug" x-text="toast.message || toast.title"></p>
                <template x-if="toast.actionUrl">
                    <a :href="toast.actionUrl" class="inline-block text-[11px] font-semibold text-zinc-900 dark:text-white underline mt-1.5 hover:text-zinc-600">
                        <span x-text="toast.actionLabel || 'View →'"></span>
                    </a>
                </template>
            </div>

            {{-- Dismiss Button --}}
            <button type="button" @click="remove(toast.id)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs p-0.5">
                ✕
            </button>
        </div>
    </template>
</div>