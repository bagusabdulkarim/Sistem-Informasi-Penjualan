<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kuitansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KuitansiController extends Controller
{
    public function index()
    {
        // Menampilkan kuitansi beserta info fakturnya
        $kuitansi = Kuitansi::with('faktur')->get();
        return response()->json(['success' => true, 'data' => $kuitansi]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kuitansi'  => 'required|unique:kuitansi,id_kuitansi',
            'id_faktur'    => 'required|exists:faktur,id_faktur|unique:kuitansi,id_faktur', // Satu faktur satu kuitansi
            'tgl_kuitansi' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kuitansi = Kuitansi::create($request->all());
        return response()->json(['success' => true, 'data' => $kuitansi], 201);
    }

    public function show($id)
    {
        // Eager Loading bertingkat: Kuitansi -> Faktur -> Pesan -> Pelanggan & DetilPesan
        $kuitansi = Kuitansi::with([
            'faktur.pesan.pelanggan',
            'faktur.pesan.detilPesan.produk'
        ])->find($id);

        if (!$kuitansi) {
            return response()->json(['message' => 'Kuitansi tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $kuitansi]);
    }

    public function destroy($id)
    {
        $kuitansi = Kuitansi::find($id);
        if (!$kuitansi) return response()->json(['message' => 'Not Found'], 404);

        $kuitansi->delete();
        return response()->json(['success' => true, 'message' => 'Kuitansi dihapus']);
    }
}
