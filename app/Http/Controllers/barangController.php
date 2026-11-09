<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class barangController extends Controller
{
    public function index(){
        $barang = Barang::all();
        return response()->json([
            'message'=>'Barang berhasil di muat',
            'success'=>true,
            'data' => $barang
        ]);
    }

    public function store(){
   
    }
}
