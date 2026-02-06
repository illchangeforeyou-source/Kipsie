<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mo1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class simpan extends Controller
{
    public function simpan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $yaps = null;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto', 'public');
            $yaps = $path; 
        }

        $data = [
            'nama' => $request->input('nama'),
            'foto' => $yaps
        ];

        $hei = new mo1();
        $hei->tambah('dok', $data);

        return redirect('/dok');
    }
}
