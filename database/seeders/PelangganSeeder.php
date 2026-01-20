<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_pelanggan' => 'P0001', 'nm_pelanggan' => 'Budi Santoso', 'alamat' => 'Jl. Teratai No.15, Bandung', 'telepon' => '081234567890', 'email' => 'budi@gmail.com'],
            ['id_pelanggan' => 'P0002', 'nm_pelanggan' => 'Sinta Dewi', 'alamat' => 'Jl. Mawar No.7, Cimahi', 'telepon' => '081288899900', 'email' => 'sinta@yahoo.com'],
            ['id_pelanggan' => 'P0003', 'nm_pelanggan' => 'Andi Pratama', 'alamat' => 'Jl. Anggrek No.5, Sukabumi', 'telepon' => '081355512345', 'email' => 'andipratama@mycompany.com'],
            ['id_pelanggan' => 'P0004', 'nm_pelanggan' => 'Dewi Larasati', 'alamat' => 'Jl. Anggrek No.11, Cimahi', 'telepon' => '081377788899', 'email' => 'dewi@gmail.com'],
            ['id_pelanggan' => 'P0005', 'nm_pelanggan' => 'Rizky Ananda', 'alamat' => 'Jl. Kenanga No.9, Garut', 'telepon' => '081366600000', 'email' => 'rizky@ymail.com'],
        ];

        foreach ($data as $item) {
            Pelanggan::create($item);
        }
    }
}
