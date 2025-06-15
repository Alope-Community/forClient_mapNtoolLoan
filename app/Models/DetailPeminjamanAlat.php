<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanAlat extends Model
{
    protected $table = 'detail_peminjaman_alat';

    protected $fillable = [
        'id_peminjaman',
        'id_unit_alat',
    ];

    public function peminjaman() {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function unitAlat() {
        return $this->belongsTo(UnitAlat::class, 'id_unit_alat');
    }
}
