<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailReservasi extends Model
{
    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }
    public function barang()
    {
    return $this->belongsTo(Barang::class);
    }
}
?>