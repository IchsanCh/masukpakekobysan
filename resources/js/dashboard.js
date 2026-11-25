/*
 * Entry khusus halaman dashboard (dimuat lewat @push('head') di dashboard/index.blade.php).
 * Dipisah dari app.js supaya halaman lain tidak membawa kode dashboard sama sekali.
 *
 * Script ini sengaja dimuat SEBELUM app.js, jadi komponen didaftarkan lewat event
 * `alpine:init` (dipicu app.js tepat sebelum Alpine memproses DOM). Alpine diambil dari
 * window.Alpine, tidak di-import lagi, supaya hanya ada satu instance.
 *
 * - countUp: angka naik dari 0 ke nilai akhir (dilewati kalau user memilih reduced motion).
 * - dashboardCharts: memuat Chart.js secara lazy (chunk terpisah), menggambar ulang saat tema
 *   berganti. Instance Chart disimpan di closure (bukan di data Alpine) supaya tidak dibungkus
 *   proxy reaktif.
 */
const prefersReducedMotion = () =>
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

document.addEventListener("alpine:init", () => {
    const Alpine = window.Alpine;

    Alpine.data("countUp", (target) => ({
        shown: target,
        init() {
            if (!target || prefersReducedMotion()) return;
            this.shown = 0;
            const start = performance.now();
            const durasi = 700;
            const tick = (now) => {
                const t = Math.min(1, (now - start) / durasi);
                this.shown = Math.round(target * (1 - Math.pow(1 - t, 3)));
                if (t < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        },
    }));

    Alpine.data("dashboardCharts", (payload) => {
        let ui = null;

        return {
            palette: { series: {} },
            visible: { masuk: true, keluar: true, disposisi: true },

            async init() {
                const { mountDashboardCharts } =
                    await import("./dashboard-charts");

                const mount = (animate) => {
                    if (ui) ui.destroy();
                    ui = mountDashboardCharts({
                        trend: this.$refs.trend,
                        payload,
                        animate,
                    });
                    this.palette = ui.palette;
                    Object.entries(this.visible).forEach(([k, on]) => {
                        if (!on) ui.setVisible(k, false);
                    });
                };

                mount(true);
                this.$watch("$store.theme.current", () =>
                    this.$nextTick(() => mount(false)),
                );
            },

            toggle(key) {
                this.visible[key] = !this.visible[key];
                if (ui) ui.setVisible(key, this.visible[key]);
            },
        };
    });
});
