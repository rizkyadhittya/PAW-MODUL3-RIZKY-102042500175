@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Transaksi Peminjaman Buku</h2>
    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="anggota_id" class="form-label">Pilih Anggota</label>
            <select name="anggota_id" id="anggota_id" class="form-control @error('anggota_id') is-invalid @enderror" required>
                <option value="">-- Pilih Anggota Peminjam --</option>
                @foreach($anggota as $item)
                    <option value="{{ $item->id }}" {{ old('anggota_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                @endforeach
            </select>
            @error('anggota_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="buku_id" class="form-label">Pilih Buku</label>
            <select name="buku_id" id="buku_id" class="form-control @error('buku_id') is-invalid @enderror" required>
                <option value="">-- Pilih Buku yang Dipinjam --</option>
                @foreach($buku as $item)
                    <option value="{{ $item->id }}" {{ old('buku_id') == $item->id ? 'selected' : '' }}>{{ $item->judul }} (Stok: {{ $item->stok }})</option>
                @endforeach
            </select>
            @error('buku_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
            @error('tanggal_pinjam')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection