<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingSales extends Model
{
    // Hapus use HasFactory;

    protected $table = 'tracking_sales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'NIK',
        'id_divisi',
        'tanggal_sales',
        'jam_sales',
        'lokasi_sales',
    ];

    public $timestamps = true;
}
