<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan halaman utama bisa merespons dengan sukses.
     *
     * Halaman ini sekarang menampilkan katalog mobil (lihat
     * User\MobilController::index()) yang melakukan query ke
     * tabel 'mobils' — sehingga RefreshDatabase wajib diaktifkan
     * agar tabel tersebut ada sebelum request dijalankan.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}