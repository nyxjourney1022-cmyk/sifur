<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['pelanggan_id', 'tanggal_transaksi', 'total_harga'];

    public function detailTransaksis(){
        return $this->hasMany(DetailTransaksi::class);
    }
    public function pelanggan(){
        return $this->belongsTo(Pelanggan::class);
    }
}
