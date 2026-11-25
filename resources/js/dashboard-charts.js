/*
 * Grafik tren dashboard (Chart.js). File ini di-import dinamis dari dashboard.js, jadi
 * Chart.js hanya diunduh di halaman dashboard. Hanya komponen yang dipakai yang
 * didaftarkan supaya bundle tetap kecil.
 */
import {
    CategoryScale,
    Chart,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from "chart.js";

Chart.register(
    CategoryScale,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

const FONT = '"DM Sans", ui-sans-serif, system-ui, sans-serif';

// Warna tiap tema. Deret dibedakan juga lewat gaya garis (disposisi putus-putus),
// jadi maknanya tidak hanya bergantung pada warna.
const PALETTES = {
    light: {
        ink: "#64748b",
        grid: "rgba(100, 116, 139, 0.16)",
        tipBg: "#0b192c",
        tipText: "#f8fafc",
        series: { masuk: "#2563eb", keluar: "#0d9488", disposisi: "#7c3aed" },
    },
    dark: {
        ink: "#97a6bd",
        grid: "rgba(151, 166, 189, 0.16)",
        tipBg: "#263754",
        tipText: "#f8fafc",
        series: { masuk: "#60a5fa", keluar: "#2dd4bf", disposisi: "#a78bfa" },
    },
};

export function mountDashboardCharts({ trend, payload, animate }) {
    const dark = document.documentElement.getAttribute("data-theme") === "dark";
    const p = dark ? PALETTES.dark : PALETTES.light;
    const reduced = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;
    const animation =
        reduced || !animate ? false : { duration: 700, easing: "easeOutQuart" };

    // Layar sempit: label sumbu lebih sedikit dan font sedikit lebih kecil
    const narrow = window.innerWidth < 640;

    Chart.defaults.font.family = FONT;
    Chart.defaults.font.size = narrow ? 11 : 12;
    Chart.defaults.color = p.ink;

    const seriesKeys = Object.keys(payload.series);
    let chart = null;

    if (trend) {
        chart = new Chart(trend, {
            type: "line",
            data: {
                labels: payload.labels,
                datasets: seriesKeys.map((k) => ({
                    label: payload.seriesLabels[k],
                    data: payload.series[k],
                    borderColor: p.series[k],
                    backgroundColor: p.series[k],
                    borderWidth: 2,
                    borderDash: k === "disposisi" ? [6, 4] : [],
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHitRadius: 16,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation,
                interaction: { mode: "index", intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: p.tipBg,
                        titleColor: p.tipText,
                        bodyColor: p.tipText,
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4,
                        usePointStyle: true,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            maxRotation: 0,
                            maxTicksLimit: narrow ? 5 : 8,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grace: "10%",
                        border: { display: false },
                        grid: { color: p.grid },
                        ticks: { precision: 0, maxTicksLimit: narrow ? 4 : 5 },
                    },
                },
            },
        });
    }

    return {
        palette: { series: p.series },
        setVisible(key, visible) {
            const i = seriesKeys.indexOf(key);
            if (!chart || i < 0) return;
            chart.setDatasetVisibility(i, visible);
            chart.update();
        },
        destroy() {
            if (chart) chart.destroy();
        },
    };
}
