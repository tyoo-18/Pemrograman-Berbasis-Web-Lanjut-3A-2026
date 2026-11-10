<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function show($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        return view(
            'peminjaman.show',
            compact('peminjaman')
        );
    }
}