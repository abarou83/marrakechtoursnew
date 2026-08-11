import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            screens: {
                'nav': '1220px',
            },
            fontFamily: {
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Terracotta - Primary (Moroccan earth tones)
                primary: {
                    DEFAULT: '#C1440E',
                    50: '#FEF7F4',
                    100: '#FCEEE7',
                    200: '#F8D4C3',
                    300: '#F4BA9F',
                    400: '#EC8657',
                    500: '#C1440E',
                    600: '#AE3D0D',
                    700: '#92330B',
                    800: '#752908',
                    900: '#5F2207',
                    950: '#3D1504',
                },
                // Bleu Majorelle - Secondary (Yves Saint Laurent blue)
                secondary: {
                    DEFAULT: '#4355BE',
                    50: '#F4F5FB',
                    100: '#E9EBF7',
                    200: '#C8CCEC',
                    300: '#A7ADE1',
                    400: '#6470CB',
                    500: '#4355BE',
                    600: '#3C4DAB',
                    700: '#32408F',
                    800: '#283373',
                    900: '#212A5E',
                    950: '#151B3D',
                },
                // Ocre doré - Accent (Moroccan gold)
                accent: {
                    DEFAULT: '#D4A843',
                    50: '#FDF9EF',
                    100: '#FBF3DF',
                    200: '#F5E2B0',
                    300: '#EFD181',
                    400: '#E2BE54',
                    500: '#D4A843',
                    600: '#BF973C',
                    700: '#9F7E32',
                    800: '#7F6428',
                    900: '#685221',
                    950: '#443515',
                },
                // Sand - Warm neutral (Desert sand)
                sand: {
                    DEFAULT: '#F5F0E8',
                    50: '#FDFCFA',
                    100: '#FAF8F4',
                    200: '#F5F0E8',
                    300: '#EBE3D5',
                    400: '#DED2BE',
                    500: '#D1C1A7',
                    600: '#BCA982',
                    700: '#9E8A63',
                    800: '#7F6F50',
                    900: '#685B42',
                    950: '#453D2C',
                },
                // Success, Warning, Danger for UI states
                success: {
                    DEFAULT: '#10B981',
                    50: '#ECFDF5',
                    500: '#10B981',
                    600: '#059669',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                    50: '#FFFBEB',
                    500: '#F59E0B',
                    600: '#D97706',
                },
                danger: {
                    DEFAULT: '#EF4444',
                    50: '#FEF2F2',
                    500: '#EF4444',
                    600: '#DC2626',
                },
                info: {
                    DEFAULT: '#3B82F6',
                    50: '#EFF6FF',
                    500: '#3B82F6',
                    600: '#2563EB',
                },
            },
            borderRadius: {
                'base': '0.75rem',
            },
            boxShadow: {
                'card': '0 2px 8px -2px rgba(0, 0, 0, 0.1), 0 4px 12px -4px rgba(0, 0, 0, 0.1)',
                'card-hover': '0 4px 16px -4px rgba(0, 0, 0, 0.15), 0 8px 24px -8px rgba(0, 0, 0, 0.15)',
                'button': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        color: theme('colors.sand.900'),
                        a: {
                            color: theme('colors.primary.500'),
                            '&:hover': {
                                color: theme('colors.primary.600'),
                            },
                        },
                        h1: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            color: theme('colors.sand.950'),
                        },
                        h2: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            color: theme('colors.sand.950'),
                        },
                        h3: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            color: theme('colors.sand.950'),
                        },
                    },
                },
            }),
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/typography'),
    ],
};
