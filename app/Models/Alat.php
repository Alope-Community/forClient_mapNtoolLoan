<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table = 'alat';
    
    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar'
    ];

    public function unitAlat()
    {
        return $this->hasMany(UnitAlat::class, 'id_alat');
    }
}
