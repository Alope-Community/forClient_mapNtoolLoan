<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peta extends Model
{
    protected $table = 'peta';

    protected $fillable = [
        'nama',
        'deskripsi',
        'nomor',
        'provinsi',
        'kabupaten',
        'gambar',
    ];
    
    public function unitPeta()
    {
        return $this->hasMany(UnitPeta::class, 'id_peta');
    }
}
