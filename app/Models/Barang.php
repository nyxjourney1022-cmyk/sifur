<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = ['nama_barang', 'kategori','stok_barang', 'harga', 'kayu_id'];

    public function kayu(){
        return $this->belongsTo(Kayu::class);
    }
    public function detailReservasis(){
        return $this->hasMany(DetailReservasi::class);
    }
    public function detailTransaksis(){
        return $this->hasMany(DetailTransaksi::class);
    }
}
