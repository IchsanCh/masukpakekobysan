{{-- Loading overlay global: tidak bisa di-klik/ditutup selama proses.
     Pasang SEKALI di layout (sebelum </body>):  <x-loading-overlay />

     Otomatis muncul di setiap submit <form> (validasi HTML lolos).
     - Form tertentu dilewati        : <form data-no-loading>   (mis. form unduh file)
     - Teks khusus per form          : <form data-loading-text="Menyimpan surat">
     - Manual (fetch/AJAX)           : window.dispatchEvent(new CustomEvent('loading-show', { detail: { text: 'Mengirim' } }))
                                       window.dispatchEvent(new CustomEvent('loading-hide'))
     Pengaman: overlay menutup sendiri setelah $failsafe ms bila halaman tidak berpindah (mis. respons berupa unduhan). --}}
@props([
    'logo' => asset('images/logo-dpmptsp.png'),
    'text' => 'Memproses data',
    'failsafe' => 45000,
])

<style>
/* Loading overlay DPMPTSP: logo "terisi warna" dari kiri ke kanan, kilau, dan bar indeterminate. */
.dpm-ld-logo { position: relative; width: 100%; aspect-ratio: 199 / 65; animation: dpm-ld-float 2.6s ease-in-out infinite; }
.dpm-ld-logo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; user-select: none; -webkit-user-drag: none; }
.dpm-ld-base { filter: grayscale(1); opacity: .16; }
.dpm-ld-fill { clip-path: inset(0 100% 0 0); animation: dpm-ld-fill 2s cubic-bezier(.65, 0, .35, 1) infinite; }
.dpm-ld-shine { position: absolute; top: -8%; bottom: -8%; left: 0; width: 16%; opacity: 0;
    background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .9), transparent);
    animation: dpm-ld-shine 2s cubic-bezier(.65, 0, .35, 1) infinite; }
.dpm-ld-bar { height: 4px; width: 100%; border-radius: 999px; background: rgba(15, 23, 42, .08); overflow: hidden; }
.dpm-ld-bar > span { display: block; height: 100%; width: 38%; border-radius: inherit;
    background: linear-gradient(90deg, #e11d0c, #f0d000, #008070); animation: dpm-ld-bar 1.3s ease-in-out infinite; }
.dpm-ld-dots::after { content: ""; animation: dpm-ld-dots 1.4s steps(1, end) infinite; }

@keyframes dpm-ld-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
@keyframes dpm-ld-fill {
    0% { clip-path: inset(0 100% 0 0); opacity: 1; }
    62% { clip-path: inset(0 0 0 0); opacity: 1; }
    84% { clip-path: inset(0 0 0 0); opacity: 1; }
    100% { clip-path: inset(0 0 0 0); opacity: 0; }
}
@keyframes dpm-ld-shine {
    0% { left: -4%; opacity: 0; }
    6% { opacity: 1; }
    62% { left: 88%; opacity: 1; }
    68%, 100% { left: 88%; opacity: 0; }
}
@keyframes dpm-ld-bar { 0% { transform: translateX(-100%); } 100% { transform: translateX(270%); } }
@keyframes dpm-ld-dots { 0% { content: ""; } 25% { content: "."; } 50% { content: ".."; } 75% { content: "..."; } }

@media (prefers-reduced-motion: reduce) {
    .dpm-ld-logo, .dpm-ld-fill, .dpm-ld-shine, .dpm-ld-bar > span, .dpm-ld-dots::after { animation: none; }
    .dpm-ld-base, .dpm-ld-shine { display: none; }
    .dpm-ld-fill { clip-path: none; }
    .dpm-ld-bar > span { width: 100%; opacity: .5; }
}
</style>

<div x-data="{
        show: false,
        text: @js($text),
        defaultText: @js($text),
        timer: null,
        open(t) {
            this.text = t || this.defaultText;
            this.show = true;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.close(), {{ (int) $failsafe }});
            document.activeElement?.blur?.();
            this.$nextTick(() => this.$refs.box?.focus({ preventScroll: true }));
        },
        close() { this.show = false; clearTimeout(this.timer); },
    }"
    @submit.window="if (!$event.defaultPrevented && !$event.target.hasAttribute('data-no-loading') && ($event.target.target || '_self') === '_self') open($event.target.dataset.loadingText)"
    @loading-show.window="open($event.detail?.text)"
    @loading-hide.window="close()"
    @pageshow.window="if ($event.persisted) close()"
    @keydown.window.capture="if (show && ['Escape', 'Tab', 'Enter', ' '].includes($event.key)) $event.preventDefault()"
    x-show="show" x-transition.opacity.duration.150ms style="display: none; touch-action: none"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-6 cursor-wait overscroll-contain bg-[#0b192c]/60 backdrop-blur-sm"
    role="alertdialog" aria-modal="true" aria-busy="true" aria-live="assertive"
    aria-labelledby="dpm-ld-label" @click.stop.prevent @contextmenu.prevent>

    <div x-ref="box" tabindex="-1"
        class="w-[min(88vw,21rem)] rounded-3xl bg-[#ffffff] px-7 pt-8 pb-7 flex flex-col items-center gap-6 outline-none
               shadow-[0_24px_70px_-18px_rgba(0,0,0,0.55)]">
        <div class="dpm-ld-logo" aria-hidden="true">
            <img class="dpm-ld-base" src="{{ $logo }}" alt="" draggable="false">
            <img class="dpm-ld-fill" src="{{ $logo }}" alt="" draggable="false">
            <span class="dpm-ld-shine"></span>
        </div>
        <div class="w-full space-y-3 text-center">
            <div class="dpm-ld-bar" aria-hidden="true"><span></span></div>
            <p id="dpm-ld-label" class="text-sm font-medium text-[#475569]"><span x-text="text"></span><span class="dpm-ld-dots" aria-hidden="true"></span></p>
            <p class="text-xs text-[#94a3b8]">Mohon tunggu, jangan tutup halaman ini.</p>
        </div>
    </div>
</div>