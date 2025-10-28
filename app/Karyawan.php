<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'NIK';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'NIK',
        'id_divisi',
        'id_subdivisi',
        'id_jabatan',
        'nama_lengkap',
        'username',
        'password',
        'no_hp',
        'tgl_lahir',
        'alamat',
        'role',
        'status',
    ];

    // ✅ Relasi ke Divisi
    public function departement()
    {
        return $this->belongsTo(Department::class, 'id_divisi', 'id_divisi');
    }

    // ✅ Relasi ke Sub Divisi
    public function subdivisi()
    {
        return $this->belongsTo(SubDivisi::class, 'id_subdivisi', 'id_subdivisi');
    }

    // ✅ Relasi ke Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    // Relasi ke Presensi
    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'NIK', 'NIK');
    }

    public function akun()
    {
        return $this->hasOne(Akun::class, 'NIK', 'NIK');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'nik', 'NIK');
    }
}
