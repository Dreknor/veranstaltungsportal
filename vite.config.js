import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    css: {
        preprocessorOptions: {},
    },
    build: {
        rollupOptions: {
            // Absolute Pfade (/fonts/...) werden zur Laufzeit aufgelöst (public-Verzeichnis)
            // und sollen von Vite nicht beim Build verarbeitet werden
            external: (id) => id.startsWith('/fonts/'),
        },
    },
});
