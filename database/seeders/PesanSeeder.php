<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesan;

class PesanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_pesan' => 1001, 'id_pelanggan' => 'P0001', 'tgl_pesan' => '2025-10-01'],
            ['id_pesan' => 1002, 'id_pelanggan' => 'P0002', 'tgl_pesan' => '2025-10-02'],
            ['id_pesan' => 1003, 'id_pelanggan' => 'P0003', 'tgl_pesan' => '2025-10-03'],
            ['id_pesan' => 1004, 'id_pelanggan' => 'P0004', 'tgl_pesan' => '2025-10-04'],
            ['id_pesan' => 1005, 'id_pelanggan' => 'P0005', 'tgl_pesan' => '2025-10-05'],
        ];

        foreach ($data as $item) {
            Pesan::create($item);
        }
    }
}
