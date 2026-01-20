<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{

    public function index() // 1. Tampilkan Semua Pelanggan
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Pelanggan',
            'data'    => Pelanggan::all()
        ], 200);
    }

    public function store(Request $request) // 2. Simpan Pelanggan Baru
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|unique:pelanggan,id_pelanggan',
            'nm_pelanggan' => 'required',
            'alamat'       => 'required',
            'telepon'      => 'required',
            'email'        => 'required|email|unique:pelanggan,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pelanggan = Pelanggan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan Berhasil Ditambahkan',
            'data'    => $pelanggan
        ], 201);
    }

    public function show($id) // 3. Detail Pelanggan
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $pelanggan], 200);
    }

    public function update(Request $request, $id) // 4. Update Pelanggan
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $pelanggan->update($request->all());
        return response()->json(['success' => true, 'message' => 'Data Diupdate', 'data' => $pelanggan], 200);
    }

    public function destroy($id) // 5. Hapus Pelanggan
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        $pelanggan->delete();
        return response()->json(['success' => true, 'message' => 'Pelanggan Dihapus'], 200);
    }
}
