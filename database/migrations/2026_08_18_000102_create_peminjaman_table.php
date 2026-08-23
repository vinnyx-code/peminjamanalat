<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('alat_id')->constrained('alat')->onUpdate('cascade')->onDelete('restrict');
            $table->dateTime('tgl_pinjam');
            $table->dateTime('tgl_harap_kembali');
            $table->enum('status', ['pending','disetujui','ditolak','selesai'])->default('pending');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
