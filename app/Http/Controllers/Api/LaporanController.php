<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesan;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use Illuminate\Http\Request;
use App\Models\DetilPesan; // Tambahkan ini
use Illuminate\Support\Facades\DB; // Tambahkan ini

class LaporanController extends Controller
{
    public function penjualanHarian()
    {
        // Mengambil semua pesan, beserta detailnya untuk dihitung
        $laporan = Pesan::with(['pelanggan', 'detilPesan'])
            ->get()
            ->map(function ($pesan) {
                // Menghitung total per nota: sum(jumlah * harga)
                $total = $pesan->detilPesan->sum(function ($item) {
                    return $item->jumlah * $item->harga;
                });

                return [
                    'nomor_nota'   => $pesan->id_pesan,
                    'tanggal'      => $pesan->tgl_pesan,
                    'pelanggan'    => $pesan->pelanggan->nm_pelanggan,
                    'jumlah_item'  => $pesan->detilPesan->count(),
                    'total_bayar'  => $total
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Laporan Penjualan',
            'data'    => $laporan,
            'grand_total' => $laporan->sum('total_bayar') // Total seluruh nota
        ]);
    }

    public function produkTerlaris() // function baru produkTerlaris
    {
        $produkTerlaris = DetilPesan::with('produk')
            ->select('id_produk', DB::raw('SUM(jumlah) as total_terjual'))
            ->groupBy('id_produk')
            ->orderBy('total_terjual', 'desc') // Urutkan dari yang paling banyak
            ->get()
            ->map(function ($item) {
                return [
                    'id_produk'     => $item->id_produk,
                    'nama_produk'   => $item->produk->nm_produk ?? 'Produk Dihapus',
                    'satuan'        => $item->produk->satuan ?? '-',
                    'total_terjual' => (int) $item->total_terjual,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Laporan Produk Terlaris',
            'data'    => $produkTerlaris
        ]);
    }

    public function exportPDF()
    {
        // 1. Ambil seluruh data pesan (tanpa filter tanggal)
        $laporan = Pesan::with(['pelanggan', 'detilPesan'])->get();

        // 2. Hitung Grand Total
        $grandTotal = 0;
        foreach ($laporan as $pesan) {
            $grandTotal += $pesan->detilPesan->sum(function ($item) {
                return $item->jumlah * $item->harga;
            });
        }

        // 3. Siapkan data untuk dikirim ke view
        $data = [
            'judul' => 'Laporan Seluruh Penjualan',
            'tanggal_cetak' => date('d-m-Y H:i:s'),
            'laporan' => $laporan,
            'grand_total' => $grandTotal
        ];

        // 4. Load View dan Download PDF
        $pdf = Pdf::loadView('laporan.penjualan_pdf', $data);

        // Gunakan download() agar file otomatis terunduh di browser/Postman
        return $pdf->download('laporan-penjualan-' . date('Ymd') . '.pdf');
    }
}
