<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingSales extends Model
{
    protected $table = 'tracking_sales';
    protected $primaryKey = 'id';
    protected $fillable = [
        'NIK',
        'tanggal_sales',
        'jam_sales',
        'lokasi_sales',
    ];

    public $timestamps = true;

    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'NIK', 'NIK');
    }
}
