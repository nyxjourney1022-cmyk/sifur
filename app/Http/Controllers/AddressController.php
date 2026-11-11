<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function index()
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();
        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'         => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:255',
            'no_tlp'        => 'required|string|max:20',
            'alamat'        => 'required|string',
            'kota'          => 'required|string|max:100',
            'provinsi'      => 'required|string|max:100',
            'kode_pos'      => 'required|string|max:10',
            'is_default'    => 'boolean',
        ]);

        $user = Auth::user();

        // Kalau set default, unset yang lain dulu
        if (!empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        // Kalau belum punya alamat, otomatis jadi default
        if ($user->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        $address = $user->addresses()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil disimpan.',
            'data'    => $address,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        $data = $request->validate([
            'label'         => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:255',
            'no_tlp'        => 'required|string|max:20',
            'alamat'        => 'required|string',
            'kota'          => 'required|string|max:100',
            'provinsi'      => 'required|string|max:100',
            'kode_pos'      => 'required|string|max:10',
            'is_default'    => 'boolean',
        ]);

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui.',
            'data'    => $address->fresh(),
        ]);
    }

    public function destroy(int $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        // Kalau yang dihapus adalah default, set yang pertama jadi default
        if ($wasDefault) {
            Auth::user()->addresses()->first()?->update(['is_default' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus.']);
    }

    public function setDefault(int $id)
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->findOrFail($id)->update(['is_default' => true]);

        return response()->json(['success' => true, 'message' => 'Alamat utama diperbarui.']);
    }
}
