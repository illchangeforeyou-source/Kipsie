<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Medicine::all()
        ]);
    }
}
