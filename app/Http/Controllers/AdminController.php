<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard()
    {
        // Hitung jumlah pengaduan untuk statistik sederhana
        $totalPengaduan = Pengaduan::count();
        $pengaduanTerbaru = Pengaduan::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('totalPengaduan', 'pengaduanTerbaru'));
    }

    // Daftar semua pengaduan
    public function daftarPengaduan()
    {
        // Gunakan pagination
        $pengaduan = Pengaduan::with('user')->latest()->paginate(10);
        
        return view('admin.pengaduan', compact('pengaduan'));
    }

    // Detail pengaduan
    public function detailPengaduan($id)
    {
        $pengaduan = Pengaduan::with('user')->findOrFail($id);
        
        return view('admin.detail', compact('pengaduan'));
    }
}
