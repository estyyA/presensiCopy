<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';

    protected $fillable = [
        'nik',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    /**
     * Relasi ke tabel karyawan
     * nik (cuti) -> NIK (karyawan)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'NIK');
    }
}
