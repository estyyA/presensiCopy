<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subdepartement extends Model
{
    protected $table = 'subdepartement';
    protected $primaryKey = 'id_subdivisi';
    public $timestamps = false;

    protected $fillable = [
        'id_divisi',
        'nama_subdivisi',
    ];

    // Relasi ke Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_divisi', 'id_divisi');
    }
}
