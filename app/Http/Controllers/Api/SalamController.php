<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalamController extends Controller
{
    public function index()
    {
        return response()->json([
            'pesan' => 'Halo! Ini pesan dari Controller.',
            'status' => 'Sukses'
        ]);
    }
}
