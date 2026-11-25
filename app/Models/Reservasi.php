<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $fillable = ['pelanggan_id', 'tanggal_reservasi', 'status', 'stok_dikurangi',];
    protected $casts = ['stok_dikurangi' => 'boolean',];

    public function pelanggan(){
        return $this->belongsTo(Pelanggan::class);
    }
    public function detailReservasis(){
        return $this->hasMany(DetailReservasi::class);
    }
}
