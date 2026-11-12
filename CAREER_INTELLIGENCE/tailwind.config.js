import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#004AAD',
                accent: '#FF44B7',
                digiblue: '#004AAD',
                digipink: '#FF44B7',
                brand: {
                    50: '#f0f5ff',
                    100: '#e0ebfe',
                    200: '#b9d5fd',
                    400: '#38bdf8',
                    500: '#004AAD',
                    600: '#003B8B',
                    700: '#002B66',
                    pink: '#FF44B7',
                },
            },
        },
    },

    plugins: [forms],
};
