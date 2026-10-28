<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->latest()->paginate(10);
        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $anggota = Anggota::orderBy('nama')->get();
        // Buku dengan stok > 0 yang boleh dipilih
        $buku = Buku::where('stok', '>', 0)->orderBy('judul')->get();

        return view('peminjaman.create', compact('anggota', 'buku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'buku_id' => 'required|exists:buku,id',
            'tanggal_pinjam' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $buku = Buku::findOrFail($request->buku_id);

            if ($buku->stok <= 0) {
                throw new \Exception('Stok buku habis.');
            }

            // Kurangi stok buku sebanyak 1
            $buku->decrement('stok');

            // Simpan data transaksi peminjaman
            Peminjaman::create([
                'anggota_id' => $request->anggota_id,
                'buku_id' => $request->buku_id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'status' => 'Dipinjam',
            ]);
        });

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman buku berhasil dicatat.');
    }

    public function kembali(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dikembalikan') {
            return redirect()->back()->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update([
                'status' => 'Dikembalikan',
                'tanggal_kembali' => now(),
            ]);

            // Tambahkan kembali stok buku sebanyak 1
            $peminjaman->buku->increment('stok');
        });

        return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dikembalikan.');
    }
}