<?php

namespace App\Http\Controllers;

use app\Models\Reservasi;
use Illuminate\Http\Request;

class reservasiController extends Controller
{
    # meminta data reservasi
    public function index(){
        $reservasi = Reservasi::all();
        return response()->json([
            'success'=> true,
            'message'=> 'Suksess mengambil',
            'data'=> $reservasi

        ]);
    }

    public function store(Request $request){
        $validatedata = $request->validate(
            ''
        );
    }
}
