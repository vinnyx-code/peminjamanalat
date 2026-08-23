<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $fillable = ['user_id','alat_id','tgl_pinjam','tgl_harap_kembali','status','petugas_id'];

    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
    public function alat(){ return $this->belongsTo(Alat::class, 'alat_id'); }
    public function pengembalian(){ return $this->hasOne(Pengembalian::class, 'peminjaman_id'); }
}
