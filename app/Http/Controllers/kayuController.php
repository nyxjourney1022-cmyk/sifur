<?php

namespace App\Http\Controllers;

use App\Models\Kayu;
use Illuminate\Http\Request;

class kayuController extends Controller
{
    public function index(){
        $kayu = Kayu::all();
        return response()->json([
            'message'=>'berhasil mengambil data kayu',
            'success' => true,
            'data' => $kayu
        ]);
    }
}
