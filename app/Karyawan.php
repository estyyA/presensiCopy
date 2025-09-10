<?php

namespace App\Models;

use App\Akun;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'Karyawan';
    protected $primaryKey = 'NIK';
    public $incrementing = false; // karena PK bukan auto increment
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'NIK',
        'id_divisi',
        'id_jabatan',
        'nama_lengkap',
        'no_hp',
        'tgl_lahir',
        'alamat',
        'role',
    ];

    // Relasi ke Departement
    public function departement()
    {
        return $this->belongsTo(Department::class, 'id_divisi', 'id_divisi');
    }

    // Relasi ke Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    // Relasi ke Presensi
    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'NIK', 'NIK');
    }

    // Relasi ke Akun
    public function akun()
    {
        return $this->hasOne(Akun::class, 'NIK', 'NIK');
    }
}
//jadi gini
