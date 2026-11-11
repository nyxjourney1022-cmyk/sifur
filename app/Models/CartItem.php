<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'barang_id', 'jumlah'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->barang->harga * $this->jumlah;
    }
}
