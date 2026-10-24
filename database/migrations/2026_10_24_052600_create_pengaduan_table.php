<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            // Relasi ke users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_tiket')->unique();
            $table->string('email');
            $table->string('nama');
            $table->string('no_telp');
            $table->string('kategori');
            $table->text('keluhan');
            // Foto bukti bersifat nullable sesuai permintaan
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
