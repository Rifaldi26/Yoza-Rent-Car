<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
    ];

    /**
     * Cari halaman berdasarkan slug, atau kembalikan instance default
     * (belum tersimpan) bila halaman belum pernah dibuat di database.
     *
     * Dipakai oleh halaman publik seperti Syarat & Ketentuan dan
     * Kebijakan Privasi, supaya tetap bisa tampil dengan rapi sebelum
     * admin mengisi kontennya lewat panel CMS.
     */
    public static function findBySlugOrDefault(string $slug, string $defaultTitle): self
    {
        $page = static::query()->where('slug', $slug)->first();

        if ($page) {
            return $page;
        }

        $page = new static([
            'slug' => $slug,
            'title' => $defaultTitle,
            'content' => json_encode([
                'sections' => [
                    ['title' => '', 'intro' => 'Konten belum tersedia.', 'items' => []],
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        // Diset langsung (bukan lewat fill mass-assignment) karena
        // 'updated_at' bukan kolom $fillable — view halaman memanggil
        // $page->updated_at->translatedFormat(...), jadi atribut ini
        // wajib berupa instance Carbon yang valid meski halaman belum
        // tersimpan ke database.
        $page->updated_at = now();

        return $page;
    }
}