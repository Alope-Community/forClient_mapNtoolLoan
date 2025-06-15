<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'id_peminjam',
        'tanggal_pinjam',
        'tanggal_pengembalian',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_peminjam');
    }

    public function detailPeminjamanAlat()
    {
        return $this->hasMany(DetailPeminjamanAlat::class, 'id_peminjaman');
    }

    public function detailPeminjamanPeta()
    {
        return $this->hasMany(DetailPeminjamanPeta::class, 'id_peminjaman');
    }
}
