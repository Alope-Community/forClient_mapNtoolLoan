<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $table = 'serial_number';

    protected $fillable = [
        'serial_number',
        'deskripsi',
    ];

    public function unitAlat() {
        return $this->hasMany(UnitAlat::class, 'id_serial_number');
    }
}
