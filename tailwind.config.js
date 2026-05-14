import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Geist', 'ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', 'Geist', 'ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#0f172a',
                muted: '#475569',
                subtle: '#64748b',
                canvas: '#ffffff',
                'canvas-soft': '#f8fafc',
                hairline: 'rgba(15,23,42,0.10)',
                accent: {
                    blue: '#4d6fff',
                    violet: '#8a6dff',
                    cyan: '#46c6ff',
                    orange: '#ff8a3d',
                    yellow: '#ffc63d',
                },
                success: {
                    text: '#166534',
                    border: '#86efac',
                    50: '#f0fdf4',
                },
                warning: {
                    text: '#92400e',
                    border: '#fcd34d',
                    50: '#fffbeb',
                },
                danger: {
                    text: '#991b1b',
                    border: '#fca5a5',
                    50: '#fef2f2',
                },
            },
            borderRadius: {
                xl: '12px',
                '2xl': '16px',
            },
            boxShadow: {
                card: '0 8px 24px rgba(15,23,42,0.05)',
                soft: '0 12px 28px rgba(15,23,42,0.06)',
            },
        },
    },

    plugins: [forms],
};
