<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Barang extends Model
{
    protected $fillable = ['nama_barang', 'kategori', 'stok_barang', 'harga', 'kayu_id', 'gambar'];

    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute(): ?string
    {
        if ($this->gambar) {
            return Storage::url($this->gambar);
        }
        return null;
    }

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
