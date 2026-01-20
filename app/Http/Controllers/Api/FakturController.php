<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakturController extends Controller
{
    public function index()
    {
        // Menampilkan faktur beserta info pesanan dan pelanggannya
        $faktur = Faktur::with('pesan.pelanggan')->get();
        return response()->json(['success' => true, 'data' => $faktur]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_faktur'  => 'required|unique:faktur,id_faktur',
            'id_pesan'   => 'required|exists:pesan,id_pesan|unique:faktur,id_pesan', // Satu pesan hanya boleh punya satu faktur
            'tgl_faktur' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $faktur = Faktur::create($request->all());
        return response()->json(['success' => true, 'data' => $faktur], 201);
    }

    public function show($id)
    {
        // Eager loading yang sangat dalam: Faktur -> Pesan -> Pelanggan & DetilPesan (Produk)
        $faktur = Faktur::with(['pesan.pelanggan', 'pesan.detilPesan.produk'])->find($id);

        if (!$faktur) {
            return response()->json(['message' => 'Faktur tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $faktur]);
    }

    public function update(Request $request, $id)
    {
        $faktur = Faktur::find($id);
        if (!$faktur) return response()->json(['message' => 'Not Found'], 404);

        $faktur->update($request->only('tgl_faktur')); // Biasanya hanya tanggal yang boleh diubah
        return response()->json(['success' => true, 'data' => $faktur]);
    }

    public function destroy($id)
    {
        // 1. Cari faktur beserta relasi kuitansinya
        $faktur = Faktur::with('kuitansi')->find($id);

        if (!$faktur) {
            return response()->json(['message' => 'Faktur tidak ditemukan'], 404);
        }

        // 2. VALIDASI: Cek apakah sudah dibayar (ada Kuitansi)
        if ($faktur->kuitansi) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Faktur tidak bisa dihapus karena sudah ada bukti pembayaran (Kuitansi).'
            ], 422);
        }

        // 3. Jika aman, hapus faktur
        $faktur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faktur berhasil dihapus.'
        ], 200);
    }
}
