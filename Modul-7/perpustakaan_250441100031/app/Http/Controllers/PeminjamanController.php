<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\Gate;

class PeminjamanController extends Controller
{
    public function show($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        Gate::authorize('view', $peminjaman);

        return view(
            'peminjaman.show',
            compact('peminjaman')
        );
    }
}
