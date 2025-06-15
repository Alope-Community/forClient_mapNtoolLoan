<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitAlat extends Model
{
    protected $table = 'unit_alat';

    protected $fillable = [
        'id_alat',
        'id_serial_number',
        'kondisi',
        'lokasi',
        'is_dipinjam',
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'id_alat');
    }

    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'id_serial_number');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjamanAlat::class, 'id_unit_alat');
    }
}
