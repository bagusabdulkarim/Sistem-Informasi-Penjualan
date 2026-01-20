<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetilPesan extends Model
{
    use HasFactory;

    protected $table = 'detil_pesan';
    // Karena tidak ada satu ID tunggal, kita beri tahu Laravel bahwa tabel ini tidak memiliki primary key standar
    protected $primaryKey = null;
    public $incrementing = false;


    protected $fillable = [
        'id_pesan',
        'id_produk',
        'jumlah',
        'harga'
    ];

    public function produk() //Relasi detail_pesan -> produk
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pesan() // Relasi detail_pesan -> pesan
    {
        return $this->belongsTo(Pesan::class, 'id_pesan', 'id_pesan');
    }

}


