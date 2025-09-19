<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatatanLaporan extends Model
{
    protected $table = 'catatan_laporan';

    protected $fillable = [
        'nik',
        'catatan',
    ];
}
