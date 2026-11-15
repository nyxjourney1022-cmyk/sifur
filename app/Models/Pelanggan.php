<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = ['user_id', 'nama', 'no_tlp', 'alamat'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
