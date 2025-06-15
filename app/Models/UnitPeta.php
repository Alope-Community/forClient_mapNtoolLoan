<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPeta extends Model
{
    protected $table = 'unit_peta';

    protected $fillable = [
        'id_peta',
        'kondisi',
        'lokasi',
        'is_dipinjam',
    ];

    public function peta() {
        return $this->belongsTo(Peta::class, 'id_peta');
    }

    public function detailPeminjaman() {
        return $this->hasMany(DetailPeminjamanPeta::class, 'id_unit_peta');
    }
}
