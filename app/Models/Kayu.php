<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kayu extends Model
{
    protected $fillable = ['jenis_kayu'];

    public function barangs(){
        return $this->hasMany(Barang::class);
    }
}
?>
