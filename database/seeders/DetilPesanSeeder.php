<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetilPesan;

class DetilPesanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_pesan' => 1001, 'id_produk' => 'PR001', 'jumlah' => 1, 'harga' => 8500000],
            ['id_pesan' => 1001, 'id_produk' => 'PR002', 'jumlah' => 2, 'harga' => 150000],
            ['id_pesan' => 1002, 'id_produk' => 'PR003', 'jumlah' => 1, 'harga' => 350000],
            ['id_pesan' => 1003, 'id_produk' => 'PR005', 'jumlah' => 3, 'harga' => 80000],
            ['id_pesan' => 1004, 'id_produk' => 'PR004', 'jumlah' => 1, 'harga' => 2100000],
            ['id_pesan' => 1005, 'id_produk' => 'PR004', 'jumlah' => 3, 'harga' => 2100000],
        ];

        foreach ($data as $item) {
            DetilPesan::create($item);
        }
    }
}
