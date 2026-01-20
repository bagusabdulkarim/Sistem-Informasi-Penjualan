<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kuitansi;

class KuitansiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_kuitansi' => 3001, 'id_faktur' => 2001, 'tgl_kuitansi' => '2025-10-06'],
            ['id_kuitansi' => 3002, 'id_faktur' => 2002, 'tgl_kuitansi' => '2025-10-07'],
            ['id_kuitansi' => 3003, 'id_faktur' => 2003, 'tgl_kuitansi' => '2025-10-08'],
            ['id_kuitansi' => 3004, 'id_faktur' => 2004, 'tgl_kuitansi' => '2025-10-09'],
            ['id_kuitansi' => 3005, 'id_faktur' => 2005, 'tgl_kuitansi' => '2025-10-10'],
        ];

        foreach ($data as $item) {
            Kuitansi::create($item);
        }
    }
}
