<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kayu extends Model
{
    public function barangs(){
        return $this->hasMany(Barang::class);
    }    
}
?>
