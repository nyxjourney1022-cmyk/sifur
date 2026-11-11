<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function index()
    {
        $user      = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();
        return view('profil', compact('user', 'addresses'));
    }
}
