<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Pemesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $account   = Account::factory()->create();
        $pemesanan = Pemesanan::factory()->create();
        $payment   = Payment::factory()->create();

        $entry = JournalEntry::create([
            'account_id'   => $account->id,
            'pemesanan_id' => $pemesanan->id,
            'payment_id'   => $payment->id,
            'debit'        => 100_000,
            'credit'       => 0,
            'description'  => 'Test entry',
            'date'         => now()->format('Y-m-d'),
        ]);

        $this->assertEquals($account->id, $entry->account_id);
        $this->assertEquals($pemesanan->id, $entry->pemesanan_id);
        $this->assertEquals($payment->id, $entry->payment_id);
        $this->assertEquals(100_000, $entry->debit);
        $this->assertEquals(0, $entry->credit);
        $this->assertEquals('Test entry', $entry->description);
    }

    public function test_belongs_to_account(): void
    {
        $account = Account::factory()->create();
        $entry   = JournalEntry::factory()->create(['account_id' => $account->id]);

        $this->assertNotNull($entry->account);
        $this->assertEquals($account->id, $entry->account->id);
    }

    public function test_belongs_to_pemesanan(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $entry     = JournalEntry::factory()->create(['pemesanan_id' => $pemesanan->id]);

        $this->assertNotNull($entry->pemesanan);
        $this->assertEquals($pemesanan->id, $entry->pemesanan->id);
    }

    public function test_belongs_to_payment(): void
    {
        $payment = Payment::factory()->create();
        $entry   = JournalEntry::factory()->create(['payment_id' => $payment->id]);

        $this->assertNotNull($entry->payment);
        $this->assertEquals($payment->id, $entry->payment->id);
    }

    public function test_debit_credit_casts_to_decimal(): void
    {
        $entry = JournalEntry::factory()->create([
            'debit'  => 123_456.78,
            'credit' => 987.65,
        ]);

        $this->assertIsString($entry->debit);
        $this->assertIsString($entry->credit);
        $this->assertEquals(123_456.78, $entry->debit);
        $this->assertEquals(987.65, $entry->credit);
    }
}