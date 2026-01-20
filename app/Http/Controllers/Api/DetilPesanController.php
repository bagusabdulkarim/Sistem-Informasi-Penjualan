<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetilPesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetilPesanController extends Controller
{
    // Menampilkan semua item di semua pesanan
    public function index()
    {
        // Kita ambil data detail beserta nama produknya
        $detil = DetilPesan::with('produk')->get();
        return response()->json(['success' => true, 'data' => $detil]);
    }

    // Menampilkan item berdasarkan id_pesan
    public function show($id_pesan)
    {
        // Mengambil semua barang yang ada dalam satu nomor pesanan
        $detil = DetilPesan::with('produk')->where('id_pesan', $id_pesan)->get();

        if ($detil->isEmpty()) {
            return response()->json(['message' => 'Detail pesanan tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $detil]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pesan'  => 'required|exists:pesan,id_pesan',
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
            'harga'     => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detil = DetilPesan::create($request->all());
        return response()->json(['success' => true, 'data' => $detil], 201);
    }

    // Karena tidak ada ID tunggal, kita mencari berdasarkan id_pesan DAN id_produk
    public function update(Request $request, $id_pesan, $id_produk)
    {
        // Cari data yang spesifik (misal: nota 1001 untuk barang PR001)
        $detil = DetilPesan::where('id_pesan', $id_pesan)
                        ->where('id_produk', $id_produk)
                        ->first();
        if (!$detil) {
            return response()->json(['message' => 'Data detail pesanan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|integer|min:1',
            'harga'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detil->update([
            'jumlah' => $request->jumlah,
            'harga'  => $request->harga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail Pesanan Berhasil Diperbarui',
            'data'    => $detil
        ], 200);
    }

    // 5. Hapus Detil Pesan
    public function destroy($id_pesan, $id_produk)
    {
        // Cari datanya dulu untuk memastikan ada
        $detil = DetilPesan::where('id_pesan', $id_pesan)
                        ->where('id_produk', $id_produk);

        if ($detil->get()->isEmpty()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $detil->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari pesanan'
        ], 200);
    }
}
