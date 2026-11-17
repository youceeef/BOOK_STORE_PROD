import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                // Editorial Bookshop Palette (Ground, Ink, Border, Accent)
                ground: {
                    DEFAULT: "#ffffff",
                    subtle: "#faf9f6",
                    dark: "#0a0a0f",
                    darkcard: "#12131a",
                },
                ink: {
                    DEFAULT: "#18181b",
                    muted: "#71717a",
                    light: "#fafafa",
                    lightmuted: "#a1a1aa",
                },
                editorial: {
                    border: "#e4e4e7",
                    borderDark: "#22232c",
                    accent: "#1e3a2f",
                    accentDark: "#c49a45",
                    accentHover: "#142820",
                },
                // Retained tokens for compatibility
                navy: {
                    800: "#1e2038",
                    900: "#1a1a2e",
                    950: "#121220",
                },
                gold: {
                    400: "#d4b86a",
                    500: "#c49a45",
                    600: "#a98230",
                },
                surface: "#faf9f6",
                darkbg: "#0a0a0f",
                darkcard: "#12131a",
                darkborder: "#22232c",
            },
            fontFamily: {
                sans: ["Inter", "Noto Sans Arabic", ...defaultTheme.fontFamily.sans],
                serif: ["Playfair Display", "Amiri", ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                'card': '0.75rem',
            }
        },
    },

    plugins: [forms],
};
