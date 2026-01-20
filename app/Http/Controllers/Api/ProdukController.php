<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk; // Import Model Produk
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        // Mengambil semua data produk dari database
        $produk = Produk::all();

        // Mengembalikan data dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Produk',
            'data'    => $produk
        ], 200);
    }

    public function show($id) // Menampilkan detail produk berdasarkan ID
    {
        // Mencari produk berdasarkan Primary Key (id_produk)
        $produk = Produk::find($id);

        // Jika data ditemukan
        if ($produk) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Data Produk',
                'data'    => $produk
            ], 200);
        }

        // Jika data tidak ditemukan
        return response()->json([
            'success' => false,
            'message' => 'Data Produk Tidak Ditemukan!',
        ], 404);
    }

    // Bagian simpan (store)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|unique:produk,id_produk|max:5',
            'nm_produk' => 'required',
            'satuan'    => 'required',
            'harga'     => 'required|numeric',
            'stock'     => 'required|integer', // VARIABEL STOCK
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $input = $request->all();

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            // Parameter 'public' di sini sangat penting agar tidak masuk ke folder private
            $image->storeAs('produk', $imageName, 'public');
            $input['gambar'] = $imageName;
        }

        $produk = Produk::create($input);
        return response()->json(['success' => true, 'message' => 'Produk Berhasil Ditambah!', 'data' => $produk], 201);
    }

    // Bagian update
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Data Tidak Ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'nm_produk' => 'required',
            'satuan'    => 'required',
            'harga'     => 'required|numeric',
            'stock'     => 'required|integer', // VARIABEL STOCK
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $input = $request->all();

        if ($request->hasFile('gambar')) {
        $image = $request->file('gambar');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        // Parameter 1: Nama folder tujuan (produk)
        // Parameter 2: Nama file
        // Parameter 3: Disk yang digunakan (public) -> ini akan otomatis ke storage/app/public
        $image->storeAs('produk', $imageName, 'public');

        $input['gambar'] = $imageName;
        }

        $produk->update($input);
        return response()->json(['success' => true, 'message' => 'Produk Berhasil Diubah!'], 200);
    }

    public function destroy($id)
    {
        // 1. Cari produk berdasarkan ID
        $produk = Produk::find($id);

        // 2. Jika data tidak ditemukan
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Data Produk Tidak Ditemukan!',
            ], 404);
        }

        // 3. Cek dan hapus file gambar jika ada
        if ($produk->gambar) {
            // KOREKSI: Gunakan disk('public') agar jalurnya sama dengan saat menyimpan
            // Jalur di dalam disk public adalah folder 'produk/'
            if (\Storage::disk('public')->exists('produk/' . $produk->gambar)) {
                \Storage::disk('public')->delete('produk/' . $produk->gambar);
            }
        }

        // 4. Hapus data dari database
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Produk Berhasil Dihapus (Beserta Gambarnya)!',
        ], 200);
    }

}
