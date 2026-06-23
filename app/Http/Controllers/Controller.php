<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Base controller.
 *
 * Laravel 11+ tidak lagi menyertakan AuthorizesRequests/ValidatesRequests
 * di stub controller secara default. Trait ini WAJIB ada di sini karena
 * beberapa controller (UlasanController, PemesananController) memanggil
 * $this->authorize(...) untuk memeriksa Policy.
 */
abstract class Controller
{
    use AuthorizesRequests;
}