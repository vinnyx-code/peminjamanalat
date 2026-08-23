<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->onUpdate('cascade')->onDelete('restrict');
            $table->string('nama_alat');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('stok')->default(0);
            $table->enum('status', ['ada','rusak','dipinjam','tidak_tersedia'])->default('ada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
