<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'Jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'nama_jabatan',
    ];

    // Relasi ke Karyawan
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'id_jabatan', 'id_jabatan');
    }
}
//jadi gini
