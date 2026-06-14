<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nomor WhatsApp Admin
    |--------------------------------------------------------------------------
    | Format: 62xxxxxxxxxx (tanpa + dan spasi)
    */
    'wa_number' => env('PAYMENT_WA_NUMBER', '6285728015695'),

    /*
    |--------------------------------------------------------------------------
    | Metode Pembayaran Tersedia
    |--------------------------------------------------------------------------
    */
    'metode' => [
        'cash' => [
            'label' => 'Cash',
            'deskripsi' => 'Bayar tunai saat pengambilan kendaraan',
            'icon' => 'cash',
            'instruksi' => 'Siapkan uang tunai sejumlah total tagihan saat pengambilan kendaraan di lokasi kami.',
        ],
        'transfer' => [
            'label' => 'Transfer Bank',
            'deskripsi' => 'Transfer ke rekening '.env('PAYMENT_TRANSFER_BANK', 'BCA'),
            'icon' => 'bank',
            'bank' => env('PAYMENT_TRANSFER_BANK', 'BCA'),
            'rekening' => env('PAYMENT_TRANSFER_REKENING', '1234567890'),
            'atas_nama' => env('PAYMENT_TRANSFER_ATAS_NAMA', 'Yoza Rent Car'),
            'instruksi' => 'Transfer ke rekening '.env('PAYMENT_TRANSFER_BANK').' '.env('PAYMENT_TRANSFER_REKENING').' a/n '.env('PAYMENT_TRANSFER_ATAS_NAMA').', lalu konfirmasi via WhatsApp.',
        ],
        'qris' => [
            'label' => 'QRIS',
            'deskripsi' => 'Scan QR Code dengan aplikasi apapun',
            'icon' => 'qris',
            'qris_image' => env('PAYMENT_QRIS_IMAGE', 'payment/qris.png'),
            'instruksi' => 'Scan QRIS dengan GoPay, OVO, Dana, ShopeePay, atau m-Banking, lalu konfirmasi via WhatsApp.',
        ],
        'edc' => [
            'label' => 'EDC / Kartu',
            'deskripsi' => 'Debit atau kredit saat pengambilan',
            'icon' => 'card',
            'instruksi' => 'Bayar menggunakan kartu debit atau kredit via mesin EDC saat pengambilan kendaraan.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Pesan WhatsApp per Metode
    |--------------------------------------------------------------------------
    | {nama}, {id}, {mobil}, {tanggal_mulai}, {tanggal_selesai},
    | {durasi}, {total}, {metode_label}, {detail_metode}
    */
    'wa_template' => [
        'cash' => implode("\n", [
            'Halo Admin Yoza Rent Car ',
            '',
            'Saya ingin konfirmasi pemesanan berikut:',
            '━━━━━━━━━━━━━━━━━━━━',
            ' ID Pemesanan : #{id}',
            ' Nama          : {nama}',
            ' Kendaraan     : {mobil}',
            ' Tanggal       : {tanggal_mulai} s/d {tanggal_selesai} ({durasi})',
            ' Total Tagihan : Rp {total}',
            '━━━━━━━━━━━━━━━━━━━━',
            ' Metode Bayar  : *CASH*',
            '',
            'Saya akan membayar tunai saat pengambilan kendaraan.',
            'Mohon konfirmasi pemesanan saya. Terima kasih! ',
        ]),
        'transfer' => implode("\n", [
            'Halo Admin Yoza Rent Car ',
            '',
            'Saya ingin konfirmasi pembayaran transfer:',
            '━━━━━━━━━━━━━━━━━━━━',
            ' ID Pemesanan : #{id}',
            ' Nama          : {nama}',
            ' Kendaraan     : {mobil}',
            ' Tanggal       : {tanggal_mulai} s/d {tanggal_selesai} ({durasi})',
            ' Total Tagihan : Rp {total}',
            '━━━━━━━━━━━━━━━━━━━━',
            ' Metode Bayar  : *TRANSFER {bank}*',
            ' Rekening      : {rekening} a/n {atas_nama}',
            '',
            'Saya sudah melakukan transfer. Bukti transfer terlampir.',
            'Mohon konfirmasi pemesanan saya. Terima kasih! ',
        ]),
        'qris' => implode("\n", [
            'Halo Admin Yoza Rent Car ',
            '',
            'Saya ingin konfirmasi pembayaran QRIS:',
            '━━━━━━━━━━━━━━━━━━━━',
            ' ID Pemesanan : #{id}',
            ' Nama          : {nama}',
            ' Kendaraan     : {mobil}',
            ' Tanggal       : {tanggal_mulai} s/d {tanggal_selesai} ({durasi})',
            ' Total Tagihan : Rp {total}',
            '━━━━━━━━━━━━━━━━━━━━',
            ' Metode Bayar  : *QRIS*',
            '',
            'Saya sudah melakukan pembayaran via QRIS. Bukti pembayaran terlampir.',
            'Mohon konfirmasi pemesanan saya. Terima kasih! ',
        ]),
        'edc' => implode("\n", [
            'Halo Admin Yoza Rent Car ',
            '',
            'Saya ingin konfirmasi pemesanan berikut:',
            '━━━━━━━━━━━━━━━━━━━━',
            ' ID Pemesanan : #{id}',
            ' Nama          : {nama}',
            ' Kendaraan     : {mobil}',
            ' Tanggal       : {tanggal_mulai} s/d {tanggal_selesai} ({durasi})',
            ' Total Tagihan : Rp {total}',
            '━━━━━━━━━━━━━━━━━━━━',
            ' Metode Bayar  : *EDC / KARTU*',
            '',
            'Saya akan membayar via kartu saat pengambilan kendaraan.',
            'Mohon konfirmasi pemesanan saya. Terima kasih! ',
        ]),
    ],

];
