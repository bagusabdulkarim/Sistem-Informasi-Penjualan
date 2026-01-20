<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk'; // Nama tabel di database
    protected $primaryKey = 'id_produk'; // Nama kolom primary key
    public $incrementing = false; // Karena id_produk bukan angka (AI)
    protected $keyType = 'string'; // Karena id_produk berisi teks (PR001)

    protected $fillable = [
        'id_produk',
        'nm_produk',
        'satuan',
        'harga',
        'stock',
        'gambar'
    ];


    protected $appends = ['url_gambar']; // Menambahkan kolom virtual ke JSON

    public function getUrlGambarAttribute()
    {
        if ($this->gambar) {
            return url('storage/produk/' . $this->gambar);
        }
        return url('storage/produk/default.png'); // Gambar jika kosong
    }
}



