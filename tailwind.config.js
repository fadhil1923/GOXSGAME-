import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import containerQueries from '@tailwindcss/container-queries';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: "#FEF9EF",
                neoYellow: "#FFE600",
                neoLime: "#4ADE80",
                neoBlue: "#2563EB",
                neoCyan: "#06B6D4",
                neoPink: "#FB7185",
                neoPurple: "#A855F7",
                neoOrange: "#FB923C",
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ["Space Grotesk", "sans-serif"],
                body: ["Plus Jakarta Sans", "sans-serif"],
                mono: ["JetBrains Mono", "monospace"],
            },
        },
    },

    plugins: [forms, containerQueries],
};
