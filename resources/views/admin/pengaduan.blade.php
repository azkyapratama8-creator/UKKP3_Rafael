@extends('layouts.admin')

@section('title', 'Daftar Pengaduan')

@section('content')
    <div class="page-header">
        <h1>Daftar Pengaduan</h1>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nomor Tiket</th>
                <th>Nama Pelapor</th>
                <th>Kategori</th>
                <th>Tanggal Pengaduan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduan as $item)
                <tr>
                    <td>{{ e($item->nomor_tiket) }}</td>
                    <td>{{ e($item->nama) }}</td>
                    <td>{{ e($item->kategori) }}</td>
                    <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.pengaduan.detail', $item->id) }}" class="btn-detail">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada pengaduan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($pengaduan->hasPages())
        <div class="pagination">
            {{ $pengaduan->links() }}
        </div>
    @endif
@endsection
