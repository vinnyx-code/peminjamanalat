<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktifitas extends Model
{
    protected $table = 'log_aktifitas';
    protected $fillable = ['user_id','aksi'];
    public $timestamps = false; // created_at already handled by DB default

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
