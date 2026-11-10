<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $myPeminjamanCount = Peminjaman::where('user_id', Auth::id())->count();
        return view('siswa.dashboard', compact('myPeminjamanCount'));
    }

    // Mengajukan Peminjaman Buku Berdasarkan ID Buku dari URL
    public function ajukanPeminjaman(Request $request, $buku_id)
    {
        // Validasi apakah buku benar-benar ada di database
        $buku = Buku::findOrFail($buku_id);

        // Cek jika siswa sedang meminjam buku yang sama dan belum dikembalikan
        $existing = Peminjaman::where('user_id', Auth::id())
            ->where('buku_id', $buku_id)
            ->whereIn('status', ['diproses', 'dipinjam', 'Pending'])
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan peminjaman untuk buku ini!');
        }

        // Simpan data peminjaman baru ke database
        Peminjaman::create([
            'user_id'        => Auth::id(),
            'buku_id'        => $buku->id,
            'tanggal_pinjam' => now(),
            'status'         => 'diproses',
        ]);

        return redirect()->route('peminjaman.show')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }

    public function riwayatPeminjaman($id)
    {
        // Pastikan siswa hanya bisa melihat riwayat miliknya sendiri
        if (Auth::id() != $id) {
            abort(403);
        }

        // Ambil data peminjaman berdasarkan user_id yang dikirim dari URL
        $riwayats = Peminjaman::with('buku')->where('user_id', $id)->get();

        return view('siswa.riwayat', compact('riwayats'));
    }
}
