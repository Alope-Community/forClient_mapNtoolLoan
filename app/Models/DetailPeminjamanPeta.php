<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanPeta extends Model
{
    protected $table = 'detail_peminjaman_peta';

    protected $fillable = [
        'id_peminjaman',
        'id_unit_peta',
    ];

    public function peminjaman() {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function unitPeta() {
        return $this->belongsTo(UnitPeta::class, 'id_unit_peta');
    }
}
