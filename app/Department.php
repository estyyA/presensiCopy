<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departement';
    protected $primaryKey = 'id_divisi';
    public $timestamps = false;

    protected $fillable = [
        'id_divisi',
        'nama_divisi',
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_divisi', 'id_divisi');
    }
}
//jadi gini
