<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    // Nama tabel disesuaikan dengan migration yang kita buat
    protected $table = 'pengaduan';

    // Mass assignment yang aman
    protected $fillable = [
        'user_id',
        'nomor_tiket',
        'email',
        'nama',
        'no_telp',
        'kategori',
        'keluhan',
        'foto',
    ];

    // Relasi: Pengaduan dimiliki oleh satu User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
