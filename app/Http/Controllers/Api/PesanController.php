<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesan;
use App\Models\DetilPesan;
use App\Models\Produk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PesanController extends Controller
{
    public function index()
    {
        // EAGER LOADING (Mengambil data pesan besertaan dengan data pelanggannya)
        $pesan = Pesan::with('pelanggan')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Transaksi Pesanan',
            'data'    => $pesan
        ], 200);
    }

    public function show($id)
    {
        // EAGER LOADING (Mengambil data pesan, pelanggannya)
        $pesan = Pesan::with(['pelanggan','detilPesan.produk'])->find($id);

        if (!$pesan) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $pesan
        ], 200);
    }

    // 3. Simpan Pesanan (Bersama Detilnya) - sudah updated
    public function store(Request $request)
    {
        // 1. Validasi Input (Header dan Array Detail)
        $validator = Validator::make($request->all(), [
            'id_pesan'     => 'required|unique:pesan,id_pesan',
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'tgl_pesan'    => 'required|date',
            'items'        => 'required|array', // Harus ada array barang
            'items.*.id_produk' => 'required|exists:produk,id_produk',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.harga'     => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // Simpan Header Pesanan
            $pesan = Pesan::create([
                'id_pesan'     => $request->id_pesan,
                'id_pelanggan' => $request->id_pelanggan,
                'tgl_pesan'    => $request->tgl_pesan,
            ]);

            // Simpan Detail Pesanan & Potong Stok
            foreach ($request->items as $item) {
                // 1. Ambil data produk terbaru
                $produk = Produk::find($item['id_produk']);

                // 2. Cek apakah stok mencukupi
                if ($produk->stock < $item['jumlah']) {
                    // Jika stok tidak cukup, lempar error agar transaksi dibatalkan (Rollback)
                    throw new \Exception("Stok produk {$produk->nm_produk} tidak mencukupi! (Sisa: {$produk->stock})");
                }

                // 3. Simpan ke detil_pesan
                DetilPesan::create([
                    'id_pesan'  => $request->id_pesan,
                    'id_produk' => $item['id_produk'],
                    'jumlah'    => $item['jumlah'],
                    'harga'     => $item['harga'],
                ]);

                // 4. Kurangi stok produk (Decrement)
                $produk->decrement('stock', $item['jumlah']);
            }

            DB::commit();

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. Update Pesanan
    public function update(Request $request, $id)
    {
        $pesan = Pesan::find($id);

        if (!$pesan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'tgl_pesan'    => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pesan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Transaksi Pesanan Berhasil Diperbarui',
            'data'    => $pesan
        ], 200);
    }

    // 5. Hapus Pesanan
    public function destroy($id)
    {
        // 1. Cari data pesan beserta detailnya
        $pesan = Pesan::with('detilPesan')->find($id);

        if (!$pesan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();

        try {
            // 2. Kembalikan stok untuk setiap produk di dalam pesanan tersebut
            foreach ($pesan->detilPesan as $item) {
                $produk = Produk::find($item->id_produk);
                if ($produk) {
                    // Gunakan increment untuk menambah stok kembali
                    $produk->increment('stock', $item->jumlah);
                }
            }

            // 3. Hapus detail pesanan terlebih dahulu (karena ada foreign key)
            // Kita gunakan query builder agar tidak perlu ID primary
            DB::table('detil_pesan')->where('id_pesan', $id)->delete();

            // 4. Hapus data pesan utama
            $pesan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan dibatalkan dan stok telah dikembalikan!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
