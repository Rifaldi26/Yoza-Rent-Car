<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PembukuanController extends Controller
{
    public function index()
    {
        $accounts = Account::withSum('journalEntries as total_debit', 'debit')
            ->withSum('journalEntries as total_credit', 'credit')
            ->orderBy('kode')
            ->get();

        $ringkasan = [
            'total_pendapatan' => JournalEntry::whereHas('account', fn ($q) => $q->where('tipe', 'pendapatan'))->sum('credit'),
            'total_pengeluaran' => JournalEntry::whereHas('account', fn ($q) => $q->where('tipe', 'pengeluaran'))->sum('debit'),
            'saldo_kas' => Account::where('kode', '1-001')->value('balance') ?? 0,
        ];

        $ringkasan['laba_rugi'] = $ringkasan['total_pendapatan'] - $ringkasan['total_pengeluaran'];

        return view('admin.pembukuan.index', compact('accounts', 'ringkasan'));
    }

    public function jurnal(Request $request)
    {
        // Query dasar (belum dipaginate) — dipakai ulang untuk hitung total
        // debit/kredit dari SELURUH data yang cocok filter, bukan cuma
        // halaman yang sedang ditampilkan.
        $query = JournalEntry::with(['account', 'pemesanan.user', 'pemesanan.mobil', 'payment'])
            ->when($request->tanggal_dari, fn ($q) => $q->whereDate('date', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->whereDate('date', '<=', $request->tanggal_sampai))
            ->when($request->account_id, fn ($q) => $q->where('account_id', $request->account_id));

        // Clone supaya $query masih bisa dipakai lagi untuk paginate() di bawah.
        $totalDebit = (clone $query)->sum('debit');
        $totalCredit = (clone $query)->sum('credit');

        $entries = $query->latest('date')
            ->paginate(20)
            ->withQueryString();

        $accounts = Account::orderBy('kode')->get();

        return view('admin.pembukuan.jurnal', compact('entries', 'accounts', 'totalDebit', 'totalCredit'));
    }

    public function laporan(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan');

        // Laba Rugi — Pendapatan
        $pendapatan = Account::where('tipe', 'pendapatan')
            ->with(['journalEntries' => function ($q) use ($tahun, $bulan) {
                $q->whereYear('date', $tahun);
                if ($bulan) {
                    $q->whereMonth('date', $bulan);
                }
            }])
            ->orderBy('kode')
            ->get()
            ->map(fn ($a) => [
                'kode' => $a->kode,
                'nama' => __($a->nama),
                'total' => $a->journalEntries->sum('credit'),
            ]);

        // Laba Rugi — Pengeluaran
        $pengeluaran = Account::where('tipe', 'pengeluaran')
            ->with(['journalEntries' => function ($q) use ($tahun, $bulan) {
                $q->whereYear('date', $tahun);
                if ($bulan) {
                    $q->whereMonth('date', $bulan);
                }
            }])
            ->orderBy('kode')
            ->get()
            ->map(fn ($a) => [
                'kode' => $a->kode,
                'nama' => __($a->nama),
                'total' => $a->journalEntries->sum('debit'),
            ]);

        $totalPendapatan = $pendapatan->sum('total');
        $totalPengeluaran = $pengeluaran->sum('total');
        $labaRugi = $totalPendapatan - $totalPengeluaran;

        // Arus Kas per bulan
        $arusKas = collect(range(1, 12))->map(function ($bln) use ($tahun) {
            $masuk = JournalEntry::whereHas('account', fn ($q) => $q->where('kode', '1-001'))
                ->whereYear('date', $tahun)->whereMonth('date', $bln)->sum('debit');
            $keluar = JournalEntry::whereHas('account', fn ($q) => $q->where('tipe', 'pengeluaran'))
                ->whereYear('date', $tahun)->whereMonth('date', $bln)->sum('debit');

            return [
                'bulan' => $bln,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'neto' => $masuk - $keluar,
            ];
        });

        return view('admin.pembukuan.laporan', compact(
            'pendapatan', 'pengeluaran',
            'totalPendapatan', 'totalPengeluaran', 'labaRugi',
            'arusKas', 'tahun', 'bulan'
        ));
    }

    public function pengeluaran(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        $account = Account::findOrFail($validated['account_id']);

        if ($account->tipe !== 'pengeluaran') {
            return back()->with('error', 'Akun yang dipilih bukan akun pengeluaran.');
        }

        $kas = Account::where('kode', '1-001')->firstOrFail();

        // Debit akun pengeluaran
        JournalEntry::create([
            'account_id' => $account->id,
            'debit' => $validated['amount'],
            'credit' => 0,
            'description' => $validated['description'],
            'date' => $validated['date'],
        ]);

        // Kredit Kas
        JournalEntry::create([
            'account_id' => $kas->id,
            'debit' => 0,
            'credit' => $validated['amount'],
            'description' => "Kas keluar — {$validated['description']}",
            'date' => $validated['date'],
        ]);

        // Update balance
        $account->increment('balance', $validated['amount']);
        $kas->decrement('balance', $validated['amount']);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $entries = JournalEntry::with('account')
            ->whereYear('date', $tahun)
            ->oldest('date')
            ->get();

        // Reuse data dari method laporan() — Pendapatan
        $pendapatan = Account::where('tipe', 'pendapatan')
            ->with(['journalEntries' => fn ($q) => $q->whereYear('date', $tahun)])
            ->orderBy('kode')->get()
            ->map(fn ($a) => [
                'kode' => $a->kode,
                'nama' => __($a->nama),
                'total' => $a->journalEntries->sum('credit'),
            ]);

        // Reuse data dari method laporan() — Pengeluaran
        $pengeluaran = Account::where('tipe', 'pengeluaran')
            ->with(['journalEntries' => fn ($q) => $q->whereYear('date', $tahun)])
            ->orderBy('kode')->get()
            ->map(fn ($a) => [
                'kode' => $a->kode,
                'nama' => __($a->nama),
                'total' => $a->journalEntries->sum('debit'),
            ]);

        $arusKas = collect(range(1, 12))->map(function ($bln) use ($tahun) {
            $masuk = JournalEntry::whereHas('account', fn ($q) => $q->where('kode', '1-001'))
                ->whereYear('date', $tahun)->whereMonth('date', $bln)->sum('debit');
            $keluar = JournalEntry::whereHas('account', fn ($q) => $q->where('tipe', 'pengeluaran'))
                ->whereYear('date', $tahun)->whereMonth('date', $bln)->sum('debit');

            return ['bulan' => $bln, 'masuk' => $masuk, 'keluar' => $keluar, 'neto' => $masuk - $keluar];
        });

        $pdf = Pdf::loadView('pdf.laporan-pembukuan', compact(
            'entries', 'pendapatan', 'pengeluaran', 'arusKas', 'tahun'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("laporan-pembukuan-{$tahun}.pdf");
    }

    public function edit(Account $account)
    {
        return view('admin.pembukuan.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->is_system) {
            return back()->with('error', 'Akun sistem tidak dapat diubah.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $account->update($validated);

        return redirect()->route('admin.pembukuan.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function inputTransaksi(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'tipe_transaksi' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        $kas = Account::where('kode', '1-001')->firstOrFail();
        $amount = $validated['amount'];
        $isDebit = $validated['tipe_transaksi'] === 'debit';

        // ── Jurnal: akun yang dipilih ───────────────────────────────────
        // Sisi jurnal SELALU mengikuti pilihan admin (debit atau kredit),
        // apa pun jenis akunnya — ini murni representasi pencatatan jurnal.
        JournalEntry::create([
            'account_id' => $account->id,
            'debit' => $isDebit ? $amount : 0,
            'credit' => $isDebit ? 0 : $amount,
            'description' => $validated['description'],
            'date' => $validated['date'],
        ]);

        // ── Jurnal: Kas sebagai lawan transaksi (balanced entry) ────────
        // Sisi jurnal Kas selalu berlawanan dengan akun yang dipilih.
        JournalEntry::create([
            'account_id' => $kas->id,
            'debit' => $isDebit ? 0 : $amount,
            'credit' => $isDebit ? $amount : 0,
            'description' => $isDebit
                ? "Kas keluar — {$validated['description']}"
                : "Kas masuk — {$validated['description']}",
            'date' => $validated['date'],
        ]);

        // ── Update balance akun yang dipilih ────────────────────────────
        // Arah perubahan saldo riil tergantung sisi normal akun tersebut:
        // - Aset & Pengeluaran  → saldo normal Debit (debit menambah saldo)
        // - Liabilitas, Modal, Pendapatan → saldo normal Kredit (kredit menambah saldo)
        $menambahSaldoAkun = $isDebit === $account->saldoNormalDebit();

        if ($menambahSaldoAkun) {
            $account->increment('balance', $amount);
        } else {
            $account->decrement('balance', $amount);
        }

        // ── Update balance Kas ───────────────────────────────────────────
        // Kas adalah akun Aset (saldo normal Debit): debit Kas = kas
        // bertambah, kredit Kas = kas berkurang. Lawan dari sisi akun pilihan.
        if ($isDebit) {
            $kas->decrement('balance', $amount);
        } else {
            $kas->increment('balance', $amount);
        }

        return back()->with('success', __('Transaksi berhasil dicatat.'));
    }
}