<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'Presensi';
    protected $primaryKey = 'id_presen';
    public $timestamps = false;

    protected $fillable = [
        'NIK',
        'tgl_presen',
        'jam_masuk',
        'jam_keluar',
        'status',
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'NIK', 'NIK');
    }
}
//jadi gini
