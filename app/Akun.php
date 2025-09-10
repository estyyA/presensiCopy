<?php

namespace App;

use App\Models\Karyawan;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Akun extends Authenticatable
{
    use Notifiable;

    protected $table = 'akun';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'NIK',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'NIK', 'NIK');
    }
}
//jadi gini
