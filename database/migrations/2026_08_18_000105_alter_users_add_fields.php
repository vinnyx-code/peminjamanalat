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
        // add nama, username, role if not exists
        if (!Schema::hasColumn('users', 'nama')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nama')->nullable()->after('name');
            });
        }
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('nama');
            });
        }
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin','petugas','peminjam'])->default('peminjam')->after('username');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }
        if (Schema::hasColumn('users', 'nama')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nama');
            });
        }
    }
};
