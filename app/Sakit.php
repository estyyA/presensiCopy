<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sakit extends Model
{
    protected $table = 'sakit';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'NIK',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'surat_dokter',
        'status_pengajuan',
        'tanggal_disetujui',
        'disetujui_oleh'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'NIK', 'NIK');
    }
}
