<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Favorit;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FavoritTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $user  = User::factory()->create();
        $mobil = Mobil::factory()->create();
        $favorit = Favorit::create([
            'user_id'  => $user->id,
            'mobil_id' => $mobil->id,
        ]);

        $this->assertEquals($user->id, $favorit->user_id);
        $this->assertEquals($mobil->id, $favorit->mobil_id);
    }

    public function test_belongs_to_user(): void
    {
        $user    = User::factory()->create();
        $mobil   = Mobil::factory()->create();
        $favorit = Favorit::create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $this->assertNotNull($favorit->user);
        $this->assertEquals($user->id, $favorit->user->id);
    }

    public function test_belongs_to_mobil(): void
    {
        $user    = User::factory()->create();
        $mobil   = Mobil::factory()->create();
        $favorit = Favorit::create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $this->assertNotNull($favorit->mobil);
        $this->assertEquals($mobil->id, $favorit->mobil->id);
    }
}