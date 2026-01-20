<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produk::create([
            'id_produk' => 'PR001',
            'nm_produk' => 'Laptop Asus A409',
            'satuan' => 'unit',
            'harga' => 8500000,
            'stock' => 10]
        );
        Produk::create([
            'id_produk' => 'PR002',
            'nm_produk' => 'Mouse Logitech M170',
            'satuan' => 'pcs',
            'harga' => 150000,
            'stock' => 30
        ]);
        Produk::create([
            'id_produk' => 'PR003',
            'nm_produk' => 'Keyboard Rexus Daxa',
            'satuan' => 'pcs',
            'harga' => 350000,
            'stock' => 20
        ]);
        Produk::create([
            'id_produk' => 'PR004',
            'nm_produk' => 'Monitor Samsung 24 inch',
            'satuan' => 'unit',
            'harga' => 2100000,
            'stock' => 15
        ]);
        produk::create([
            'id_produk' => 'PR005',
            'nm_produk' => 'Flashdisk Sandisk 32GB',
            'satuan' => 'pcs',
            'harga' => 8000,
            'stock' => 50
        ]);
    }
}
