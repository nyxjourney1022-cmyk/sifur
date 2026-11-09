<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kayu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

    public function store(Request $request){
        $validate = $request -> validate([
            'nama_barang' => 'required|string|max:255',
            'stock_barang' => 'requred|int|min:1',
            'harga' => 'required|float|nullable|min:1',
            'jenis_kayu' => 'required|int|min:1'
        ]);
        DB::beginTransaction();
        try{
            $kayu = Kayu::firstOrCreate(
                ['nama_kayu' =>$request->namaKayu]
            );
            $barang = Barang::create([
                'nama_barang' => $request-> namaBarang,
                'stock_barang' => $request -> stockBarang,
                'harga' => $request -> harga,
                'jenis_kayu' => $kayu -> id
            ]);
            DB::commit();
            return response()-> json([
                'message' => 'Berhasil dimasukan kedatabase',
                'data_barang' => $barang
            ],201);
        }catch(\Exception $e){
            DB::rollBack();
            return response() -> json([
                'message' => 'Barang tidak berhasil dimasukan kedatabase',
                'error' => $e -> getMessage()
                ],500);
        }
    }

}
