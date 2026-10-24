<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\TicketReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // Menampilkan halaman form pengaduan customer
    public function showPengaduan()
    {
        // Alokasikan nomor tiket secara aman di backend
        $nomorTiket = $this->alokasiNomorTiket();
        
        return view('customer.pengaduan', compact('nomorTiket'));
    }

    // Fungsi untuk mengalokasikan nomor tiket secara aman (tahan terhadap request bersamaan)
    private function alokasiNomorTiket()
    {
        $user = Auth::user();
        $tanggal = date('Ymd');
        $prefix = 'TKT-' . $tanggal . '-';
        
        // Coba alokasikan tiket dalam transaksi database untuk menghindari duplikasi
        return DB::transaction(function () use ($user, $prefix) {
            // Cari nomor urut terakhir untuk hari ini
            $lastReservation = TicketReservation::where('nomor_tiket', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('nomor_tiket', 'desc')
                ->first();
            
            $lastPengaduan = Pengaduan::where('nomor_tiket', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('nomor_tiket', 'desc')
                ->first();
            
            // Tentukan nomor urut berikutnya
            $lastNumber = 0;
            if ($lastReservation && preg_match('/-(\d{3})$/', $lastReservation->nomor_tiket, $matches)) {
                $lastNumber = max($lastNumber, intval($matches[1]));
            }
            if ($lastPengaduan && preg_match('/-(\d{3})$/', $lastPengaduan->nomor_tiket, $matches)) {
                $lastNumber = max($lastNumber, intval($matches[1]));
            }
            
            $nextNumber = $lastNumber + 1;
            $nomorTiket = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Reservasi tiket ini untuk user saat ini
            TicketReservation::create([
                'nomor_tiket' => $nomorTiket,
                'user_id' => $user->id,
                'status' => 'reserved',
            ]);
            
            return $nomorTiket;
        });
    }

    // Submit pengaduan tanpa reload (AJAX)
    public function submitPengaduan(Request $request)
    {
        // Validasi di backend
        $validator = Validator::make($request->all(), [
            'nomor_tiket' => 'required|string',
            'email' => 'required|email',
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'kategori' => 'required|string|max:255',
            'keluhan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB, hanya gambar
        ], [
            'nomor_tiket.required' => 'Nomor tiket wajib ada.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nama.required' => 'Nama pelapor wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'no_telp.required' => 'Nomor HP wajib diisi.',
            'no_telp.max' => 'Nomor HP maksimal 20 karakter.',
            'kategori.required' => 'Kategori pengaduan wajib diisi.',
            'kategori.max' => 'Kategori maksimal 255 karakter.',
            'keluhan.required' => 'Keluhan wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verifikasi tiket yang direservasi milik user ini dan masih berstatus reserved
        $reservation = TicketReservation::where('nomor_tiket', $request->nomor_tiket)
            ->where('user_id', $user->id)
            ->where('status', 'reserved')
            ->first();
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor tiket tidak valid atau sudah digunakan.'
            ], 422);
        }
        
        // Pastikan nomor tiket belum pernah digunakan di pengaduan
        if (Pengaduan::where('nomor_tiket', $request->nomor_tiket)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor tiket sudah digunakan.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $fotoPath = null;
            // Simpan foto menggunakan Laravel Storage jika ada upload
            if ($request->hasFile('foto')) {
                // Simpan di storage/app/public/bukti
                $fotoPath = $request->file('foto')->store('bukti', 'public');
            }
            
            // Simpan pengaduan
            Pengaduan::create([
                'user_id' => $user->id,
                'nomor_tiket' => $request->nomor_tiket,
                'email' => $request->email,
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'kategori' => $request->kategori,
                'keluhan' => $request->keluhan,
                'foto' => $fotoPath,
            ]);
            
            // Update status reservasi menjadi used
            $reservation->update(['status' => 'used']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dikirim!',
                'nomor_tiket' => $request->nomor_tiket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Jika terjadi error, hapus foto yang mungkin sudah terupload
            if (isset($fotoPath) && $fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pengaduan. Silakan coba lagi.'
            ], 500);
        }
    }
}
