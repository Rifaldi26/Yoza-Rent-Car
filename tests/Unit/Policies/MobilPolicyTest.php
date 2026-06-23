<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Mobil;
use App\Policies\MobilPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test MobilPolicy.
 *
 * Catatan: saat ini Admin\MobilController belum memanggil
 * $this->authorize() — otorisasi praktis ditegakkan oleh middleware
 * route 'is_admin'. Policy ini tetap diuji sebagai unit independen
 * agar siap dipakai jika controller di-refactor untuk memanggilnya
 * secara eksplisit (lebih idiomatis & konsisten dengan PemesananPolicy).
 */
final class MobilPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MobilPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new MobilPolicy();
    }

    public function test_hanya_admin_yang_diizinkan_untuk_semua_aksi(): void
    {
        $admin = $this->buatAdmin();
        $user = $this->buatUser();
        $mobil = Mobil::factory()->create();

        $this->assertTrue($this->policy->viewAny($admin));
        $this->assertFalse($this->policy->viewAny($user));

        $this->assertTrue($this->policy->create($admin));
        $this->assertFalse($this->policy->create($user));

        $this->assertTrue($this->policy->update($admin, $mobil));
        $this->assertFalse($this->policy->update($user, $mobil));

        $this->assertTrue($this->policy->delete($admin, $mobil));
        $this->assertFalse($this->policy->delete($user, $mobil));

        $this->assertTrue($this->policy->toggleStatus($admin, $mobil));
        $this->assertFalse($this->policy->toggleStatus($user, $mobil));
    }
}
