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
                    50: '#FDF1F2',
                    100: '#FBDFE0',
                    200: '#F7BABD',
                    300: '#F2888D',
                    400: '#EB5158',
                    500: '#E72A32',
                    600: '#E6212A', // warna utama logo
                    700: '#B7151C',
                    800: '#931116',
                    900: '#6E0C11',
                    950: '#40070A',
                },
            },
        },
    },

    plugins: [
        require('@tailwindcss/typography'),
    ],
};