<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'user_id',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'catatan',
    ];

    // Relasi ke tabel users
    public function user()
{
    return $this->belongsTo(\App\User::class, 'user_id');
}
}
