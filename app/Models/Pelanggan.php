<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = ['nama', 'no_tlp', 'alamat'];

    public function reservasis(){
        return $this->hasMany(Reservasi::class);
    }
    public function transaksis(){
        return $this->hasMany(Transaksi::class);
    }
}
