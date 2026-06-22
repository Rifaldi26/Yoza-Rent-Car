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
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            // ── Warna brand ────────────────────────────────────────────
            //
            // 'primary' diturunkan dari warna merah pada logo resmi
            // Yoza Rent Car (lihat public/images/logo.png). Pakai token
            // ini (mis. bg-primary-600, text-primary-700) untuk elemen
            // aksi/brand — tombol utama, link, state aktif, dsb.
            //
            // JANGAN dipakai untuk warna status (sukses/gagal/peringatan/
            // info) — itu tetap pakai palet bawaan Tailwind (green-*,
            // red-*, yellow-*, blue-*) supaya makna warnanya tidak
            // tertukar dengan warna brand.
            colors: {
                primary: {
                    50:  '#EEF2FB',
                    100: '#E2E9F9',
                    200: '#C0D1F1',
                    300: '#92AFE7',
                    400: '#608ADC',
                    500: '#3D70D4',
                    600: '#3B6FD4', // warna biru lama
                    700: '#2E5BB8', // hover/darker lama
                    800: '#1D4186',
                    900: '#163164',
                    950: '#0D1C3B',
                },
            },
        },
    },

    plugins: [
        require('@tailwindcss/typography'),
    ],
};