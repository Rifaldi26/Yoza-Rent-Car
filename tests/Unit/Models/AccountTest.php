<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $account = Account::create([
            'kode'       => '1-001',
            'nama'       => 'Kas',
            'tipe'       => 'aset',
            'balance'    => 0,
            'is_system'  => true,
        ]);

        $this->assertEquals('1-001', $account->kode);
        $this->assertEquals('Kas', $account->nama);
        $this->assertEquals('aset', $account->tipe);
        $this->assertEquals(0.0, $account->balance);
        $this->assertTrue($account->is_system);
    }

    public function test_journal_entries_relation(): void
    {
        $account = Account::factory()->create();
        $entry   = JournalEntry::factory()->create(['account_id' => $account->id]);

        $this->assertTrue($account->journalEntries->contains($entry));
    }

    public function test_total_debit(): void
    {
        $account = Account::factory()->create();
        JournalEntry::factory()->count(2)->create([
            'account_id' => $account->id,
            'debit'      => 50_000,
            'credit'     => 0,
        ]);

        $this->assertEquals(100_000, $account->totalDebit());
    }

    public function test_total_credit(): void
    {
        $account = Account::factory()->create();
        JournalEntry::factory()->create([
            'account_id' => $account->id,
            'debit'      => 0,
            'credit'     => 75_000,
        ]);

        $this->assertEquals(75_000, $account->totalCredit());
    }
}