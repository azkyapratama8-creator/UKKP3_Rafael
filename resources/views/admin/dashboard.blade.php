@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1>Dashboard Admin</h1>
    </div>

    <div class="stats-card">
        <h3>Total Pengaduan</h3>
        <p class="stats-number">{{ $totalPengaduan }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Pengaduan Terbaru</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nomor Tiket</th>
                    <th>Nama Pelapor</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduanTerbaru as $pengaduan)
                    <tr>
                        <td>{{ e($pengaduan->nomor_tiket) }}</td>
                        <td>{{ e($pengaduan->nama) }}</td>
                        <td>{{ e($pengaduan->kategori) }}</td>
                        <td>{{ $pengaduan->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.pengaduan.detail', $pengaduan->id) }}" class="btn-detail">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada pengaduan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 15px;">
            <a href="{{ route('admin.pengaduan') }}" class="btn-link">Lihat Semua Pengaduan →</a>
        </div>
    </div>
@endsection
