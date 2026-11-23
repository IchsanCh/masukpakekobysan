import "./bootstrap";
import Alpine from "alpinejs";

window.Alpine = Alpine;

/*
 * Tema terang/gelap.
 * Urutan: pilihan yang tersimpan di localStorage -> kalau belum ada, ikuti OS.
 * Pilihan hanya disimpan saat user menekan tombol toggle, jadi selama belum
 * pernah memilih, tema tetap mengikuti perubahan tema OS.
 * (Atribut data-theme pertama kali dipasang oleh <x-theme-init> di <head>
 * supaya tidak ada kedip saat halaman dimuat.)
 */
const THEME_KEY = "theme";
const osDark = window.matchMedia("(prefers-color-scheme: dark)");

const readStored = () => {
    try {
        const value = localStorage.getItem(THEME_KEY);
        return value === "light" || value === "dark" ? value : null;
    } catch (e) {
        return null; // localStorage diblokir (mode privat tertentu, dll.)
    }
};

const resolveTheme = () => readStored() ?? (osDark.matches ? "dark" : "light");

const applyTheme = (theme) => {
    document.documentElement.setAttribute("data-theme", theme);
};

Alpine.store("theme", {
    current: resolveTheme(),

    set(theme) {
        this.current = theme;
        applyTheme(theme);
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (e) {
            // gagal menyimpan: tema tetap berlaku untuk sesi ini
        }
    },

    toggle() {
        this.set(this.current === "dark" ? "light" : "dark");
    },

    // dipanggil saat OS / tab lain mengubah tema, tanpa menulis ke localStorage
    sync() {
        this.current = resolveTheme();
        applyTheme(this.current);
    },
});

// Belum pernah memilih -> ikuti perubahan tema OS secara langsung
osDark.addEventListener("change", () => {
    if (readStored() === null) Alpine.store("theme").sync();
});

// Tab lain mengganti tema -> ikut berubah
window.addEventListener("storage", (e) => {
    if (e.key === THEME_KEY) Alpine.store("theme").sync();
});

Alpine.start();
