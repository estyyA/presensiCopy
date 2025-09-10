<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'Departement';
    protected $primaryKey = 'id_divisi';
    public $timestamps = false;

    protected $fillable = [
        'nama_divisi',
    ];

    // Relasi ke Karyawan
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'id_divisi', 'id_divisi');
    }
}
//jadi gini
