@extends('layouts.admin')

@section('title', 'Detail Pengaduan')

@section('content')
    <div class="page-header">
        <h1>Detail Pengaduan</h1>
    </div>

    <div class="detail-card">
        <div class="detail-row">
            <div class="detail-label">Nomor Tiket</div>
            <div class="detail-value">{{ e($pengaduan->nomor_tiket) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Nama Pelapor</div>
            <div class="detail-value">{{ e($pengaduan->nama) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email</div>
            <div class="detail-value">{{ e($pengaduan->email) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Nomor HP</div>
            <div class="detail-value">{{ e($pengaduan->no_telp) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Kategori</div>
            <div class="detail-value">{{ e($pengaduan->kategori) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Keluhan</div>
            <div class="detail-value detail-text">{{ e($pengaduan->keluhan) }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Foto Bukti</div>
            <div class="detail-value">
                @if($pengaduan->foto)
                    <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Foto Bukti" class="foto-bukti">
                @else
                    <span>Tidak ada foto bukti</span>
                @endif
            </div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Tanggal Pengaduan</div>
            <div class="detail-value">{{ $pengaduan->created_at->format('d-m-Y H:i') }}</div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('admin.pengaduan') }}" class="btn-back">← Kembali ke Daftar Pengaduan</a>
    </div>
@endsection
