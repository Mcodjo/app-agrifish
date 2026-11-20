import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/site.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Figtree Variable"', ...defaultTheme.fontFamily.sans],
                serif: ['"Newsreader Variable"', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                primary: {
                    DEFAULT: '#2d6a4f',
                    50: '#eff5f1',
                    100: '#dae9df',
                    200: '#b5d3c0',
                    300: '#87b69a',
                    400: '#559574',
                    500: '#2d6a4f',
                    600: '#265c44',
                    700: '#204e3a',
                    800: '#1b4332',
                    900: '#132f24',
                },
                'primary-dark': '#1b4332',
                'primary-light': '#3f8463',
                gold: '#b8862b',
                'gold-light': '#e2bf6e',
                'gold-dark': '#8f6720',
                cream: '#f4f2ec',
                sand: '#ebe7dc',
                line: '#e4e0d6',
                dark: '#17211b',
                muted: '#5c665f',
                night: '#10231a',
            },
            borderRadius: {
                panel: '28px',
            },
        },
    },

    plugins: [forms],
};
