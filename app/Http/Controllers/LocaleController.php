<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $supported = ['id', 'en'];

        if (in_array($locale, $supported)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}