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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#2d6a4f',
                    50: '#edf7f1',
                    100: '#d8eadf',
                    200: '#b2d5c1',
                    300: '#86bc9f',
                    400: '#5fa47f',
                    500: '#2d6a4f',
                    600: '#265b43',
                    700: '#1f4b37',
                    800: '#17392a',
                    900: '#10261c',
                },
                'primary-dark': '#1b4332',
                'primary-light': '#4c956c',
                gold: '#c9952a',
                'gold-light': '#f4c542',
                'gold-dark': '#a67c1b',
                cream: '#f8f6f0',
                dark: '#1a2e1a',
            },
        },
    },

    plugins: [forms],
};
