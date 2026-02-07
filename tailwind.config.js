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
            // Fluent 2 Design System Typography
            fontFamily: {
                sans: [
                    'Segoe UI',
                    'system-ui',
                    '-apple-system',
                    'BlinkMacSystemFont',
                    'sans-serif',
                ],
            },
            
            // Fluent 2 Semantic Color Tokens
            colors: {
                // Page-specific theme colors (Fluent Design 2)
                theme: {
                    home: {
                        DEFAULT: '#346AF0',
                        light: '#EBF3FF',
                        lighter: '#F5F8FF',
                        dark: '#2C58D2',
                    },
                    jobs: {
                        DEFAULT: '#8B5CF6',
                        light: '#F3EBFF',
                        lighter: '#F9F5FF',
                        dark: '#7C3AED',
                    },
                    education: {
                        DEFAULT: '#10B981',
                        light: '#ECFDF5',
                        lighter: '#F0FDF9',
                        dark: '#059669',
                    },
                    employers: {
                        DEFAULT: '#EF4444',
                        light: '#FEF2F2',
                        lighter: '#FEF7F7',
                        dark: '#DC2626',
                    },
                },
                // Brand - Main brand colors (desaturated for calm feel)
                brand: {
                    primary: '#346AF0',
                    primaryHover: '#2C58D2',
                    primaryPressed: '#1E3A8A',
                },
                // Primary - Main brand color
                primary: {
                    DEFAULT: '#346AF0',
                    hover: '#2C58D2',
                    pressed: '#1E3A8A',
                    light: '#EBF3FF',
                    dark: '#1E3A8A',
                    border: '#346AF0',
                },
                // Secondary - Supporting brand color (more subtle)
                secondary: {
                    DEFAULT: '#008272',
                    hover: '#006B5E',
                    pressed: '#004D42',
                    light: '#E6F5F3',
                    dark: '#004D42',
                },
                // Accent - Highlight actions
                accent: {
                    DEFAULT: '#00B294',
                    hover: '#009578',
                    pressed: '#007A63',
                    light: '#E6F7F4',
                    dark: '#007A63',
                    success: '#107C10',
                    successHover: '#0E6B0E',
                    successPressed: '#0A5A0A',
                },
                // Semantic colors
                success: {
                    DEFAULT: '#107C10',
                    hover: '#0E6B0E',
                    pressed: '#0A5A0A',
                    light: '#E7F4E7',
                    dark: '#0A5A0A',
                    50: '#F0F9F0',
                    100: '#E7F4E7',
                    600: '#107C10',
                },
                warning: {
                    DEFAULT: '#FFB900',
                    hover: '#E6A700',
                    pressed: '#CC9400',
                    light: '#FFF8E1',
                    dark: '#CC9400',
                    50: '#FFFBF0',
                    100: '#FFF8E1',
                    600: '#FFB900',
                },
                danger: {
                    DEFAULT: '#D13438',
                    hover: '#B82D31',
                    pressed: '#A1262A',
                    light: '#FCE8E9',
                    dark: '#A1262A',
                    50: '#FEF0F1',
                    100: '#FCE8E9',
                    600: '#D13438',
                },
                info: {
                    DEFAULT: '#346AF0',
                    light: '#EBF3FF',
                    dark: '#1E3A8A',
                    50: '#F5F8FF',
                    100: '#EBF3FF',
                    600: '#346AF0',
                },
                // Neutrals - Text and surfaces (Fluent 2 refined)
                text: {
                    primary: '#323130',
                    secondary: '#605E5C',
                    tertiary: '#8A8886',
                    disabled: '#C8C6C4',
                    inverse: '#FFFFFF',
                },
                background: {
                    DEFAULT: '#FAFAFA',
                    base: '#FFFFFF',
                    secondary: '#FAF9F8',
                    tertiary: '#F3F2F1',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    base: '#FFFFFF',
                    1: '#FAF9F8',
                    2: '#F3F2F1',
                    light: '#FAF9F8',
                    secondary: '#F3F2F1',
                    tinted: '#FAFAFA',
                    white: '#FFFFFF',
                    subtle: '#FAFAFA',
                },
                stroke: {
                    default: '#E1DFDD',
                    subtle: '#F3F2F1',
                },
                border: {
                    DEFAULT: '#E1DFDD',
                    subtle: '#F3F2F1',
                    border: '#E1DFDD',
                    light: '#EDEBE9',
                    dark: '#8A8886',
                },
                // Fluent Control States
                control: {
                    fill: '#F3F2F1',
                    'fill-hover': '#EDEBE9',
                    'fill-pressed': '#E1DFDD',
                    border: '#E1DFDD',
                    'border-hover': '#C8C6C4',
                    'border-focus': '#346AF0',
                },
            },

            // Fluent 2 Spacing Scale (comfortable, airy)
            spacing: {
                'xs': '4px',
                'sm': '8px',
                'md': '12px',
                'lg': '16px',
                'xl': '20px',
                '2xl': '24px',
                '3xl': '32px',
                '4xl': '40px',
                '5xl': '48px',
                '6xl': '64px',
            },

            // Fluent 2 Border Radius (soft, rounded)
            borderRadius: {
                'none': '0',
                'sm': '2px',
                'DEFAULT': '4px',
                'md': '6px',
                'lg': '8px',
                'xl': '12px',
                '2xl': '16px',
                '3xl': '20px',
                'full': '9999px',
                // Fluent semantic names
                'control': '12px',
                'card': '16px',
                'panel': '20px',
            },

            // Fluent 2 Border Width (hairline support)
            borderWidth: {
                'DEFAULT': '1px',
                '0': '0',
                '2': '2px',
                'hairline': '0.5px',
            },

            // Fluent 2 Elevation System
            boxShadow: {
                'none': 'none',
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                'DEFAULT': '0 1px 4px 0 rgba(0, 0, 0, 0.08)',
                'md': '0 2px 8px 0 rgba(0, 0, 0, 0.12)',
                'lg': '0 4px 16px 0 rgba(0, 0, 0, 0.16)',
                'xl': '0 8px 24px 0 rgba(0, 0, 0, 0.20)',
                // Fluent elevation tokens
                'e0': 'none',
                'e1': '0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                'e2': '0 4px 8px 0 rgba(0, 0, 0, 0.10)',
                'elevation-0': 'none',
                'elevation-1': '0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                'elevation-2': '0 4px 8px 0 rgba(0, 0, 0, 0.10)',
                'elevation-3': '0 8px 16px 0 rgba(0, 0, 0, 0.14)',
                'card': '0 2px 8px 0 rgba(0, 0, 0, 0.08)',
                'hover': '0 4px 12px 0 rgba(0, 0, 0, 0.14)',
            },

            // Fluent 2 Typography Scale
            fontSize: {
                'caption': ['12px', { lineHeight: '16px', fontWeight: '400' }],
                'body-sm': ['13px', { lineHeight: '18px', fontWeight: '400' }],
                'body': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'body-lg': ['16px', { lineHeight: '22px', fontWeight: '400' }],
                'subtitle': ['18px', { lineHeight: '24px', fontWeight: '600' }],
                'title-3': ['20px', { lineHeight: '28px', fontWeight: '600' }],
                'title-2': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'title-1': ['28px', { lineHeight: '36px', fontWeight: '600' }],
                'display': ['32px', { lineHeight: '40px', fontWeight: '600' }],
                'large-display': ['40px', { lineHeight: '52px', fontWeight: '600' }],
            },

            // Fluent 2 Motion Tokens
            transitionDuration: {
                '120': '120ms',
                '160': '160ms',
                '200': '200ms',
                'fast': '100ms',
                'normal': '200ms',
                'slow': '300ms',
            },

            transitionTimingFunction: {
                'fluent-enter': 'cubic-bezier(0.0, 0.0, 0.2, 1.0)',
                'fluent-exit': 'cubic-bezier(0.4, 0.0, 1.0, 1.0)',
            },

            // Z-index layering
            zIndex: {
                'dropdown': '1000',
                'modal': '1050',
                'popover': '1060',
                'tooltip': '1070',
            },
        },
    },

    plugins: [forms],
};
