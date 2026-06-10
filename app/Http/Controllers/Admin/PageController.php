<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Menampilkan daftar halaman yang bisa diedit
    public function index()
    {
        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    // Menampilkan form editor WYSIWYG
    public function edit($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('admin.pages.edit', compact('page'));
    }

    // Menyimpan perubahan ke database
    public function update(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
    
        $content = ['sections' => $request->input('sections', [])];
    
        $page->update([
            'title'   => $request->title,
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
        ]);
    
        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }
}