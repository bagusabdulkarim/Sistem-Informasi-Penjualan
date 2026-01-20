<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faktur;

class FakturSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_faktur' => 2001, 'id_pesan' => 1001, 'tgl_faktur' => '2025-10-06'],
            ['id_faktur' => 2002, 'id_pesan' => 1002, 'tgl_faktur' => '2025-10-07'],
            ['id_faktur' => 2003, 'id_pesan' => 1003, 'tgl_faktur' => '2025-10-08'],
            ['id_faktur' => 2004, 'id_pesan' => 1004, 'tgl_faktur' => '2025-10-09'],
            ['id_faktur' => 2005, 'id_pesan' => 1005, 'tgl_faktur' => '2025-10-10'],
        ];

        foreach ($data as $item) {
            Faktur::create($item);
        }
    }
}
