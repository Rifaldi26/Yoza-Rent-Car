<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

/**
 * PageController (publik)
 *
 * Menampilkan halaman statis yang kontennya dikelola admin lewat
 * CMS sederhana (lihat App\Http\Controllers\Admin\PageController).
 * Tidak butuh login — bisa diakses siapa saja.
 */
final class PageController extends Controller
{
    public function terms(): View
    {
        $page = Page::findBySlugOrDefault('terms', 'Syarat dan Ketentuan');

        return view('pages.terms', compact('page'));
    }

    public function privacy(): View
    {
        $page = Page::findBySlugOrDefault('privacy', 'Pemberitahuan Privasi');

        return view('pages.privacy', compact('page'));
    }
}
