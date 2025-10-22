<?php

namespace App;
use App\Department;


use Illuminate\Database\Eloquent\Model;

class TrackingSales extends Model
{
use HasFactory;

    protected $table = 'tracking_sales';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'NIK',
        'id_divisi',
        'id_subdivisi',
        'tanggal_sales',
        'jam_sales',
        'lokasi_sales',
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'NIK', 'NIK');
    }

    // Relasi ke Divisi
    public function divisi()
    {
        return $this->belongsTo(Department::class, 'id_divisi', 'id_divisi');
    }

    // Relasi ke Subdepartement
    public function subdepartement()
    {
        return $this->belongsTo(Subdepartement::class, 'id_subdivisi', 'id_subdivisi');
    }
}
