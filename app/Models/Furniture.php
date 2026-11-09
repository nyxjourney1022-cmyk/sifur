<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    protected $table    = 'furnitures';
    protected $fillable = ['nama_toko', 'alamat_toko', 'no_tlp'];
}
