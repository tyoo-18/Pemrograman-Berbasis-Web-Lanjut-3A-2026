<h2>Detail Peminjaman</h2>

<p>ID: {{ $peminjaman->id }}</p>
<p>Nama Siswa: {{ $peminjaman->user->name }}</p>
<p>Judul Buku: {{ $peminjaman->buku->judul ?? '-' }}</p>
<p>Status: {{ $peminjaman->status }}</p>