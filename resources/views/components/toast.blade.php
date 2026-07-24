{{--
    Toast Notification — taruh di layout app.blade.php
    Otomatis baca session('success'), session('error'), session('warning'), session('info')
--}}

<div x-cloak
     x-data="{
        toasts: [],
        counter: 0,

        init() {
            const flash = {
                success: @js(session('success')),
                error: @js(session('error')),
                warning: @js(session('warning')),
                info: @js(session('info')),
            };
            Object.entries(flash).forEach(([type, msg]) => {
                if (msg) this.show(msg, type);
            });
        },

        show(message, type = 'info', duration = 4500) {
            const id = ++this.counter;
            this.toasts.push({ id, message, type, duration, visible: true });
            setTimeout(() => this.dismiss(id), duration);
        },

        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) {
                t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
            }
        }
     }"
     class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="alert shadow-lg relative overflow-hidden pointer-events-auto"
             :class="{
                 'alert-success': toast.type === 'success',
                 'alert-error': toast.type === 'error',
                 'alert-warning': toast.type === 'warning',
                 'alert-info': toast.type === 'info',
             }">

            {{-- Icon --}}
            <template x-if="toast.type === 'success'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </template>

            <span x-text="toast.message" class="text-sm flex-1"></span>

            {{-- Close --}}
            <button @click="dismiss(toast.id)" class="btn btn-ghost btn-xs btn-circle shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            {{-- Progress bar --}}
            <div class="absolute bottom-0 left-0 h-0.5 bg-current opacity-20"
                 :style="'animation: toastProgress ' + toast.duration + 'ms linear forwards'"></div>
        </div>
    </template>
</div>

<style>
@keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
[x-cloak] { display: none !important; }
</style>
