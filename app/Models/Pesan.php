<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    use HasFactory;

    protected $table = 'pesan';
    protected $primaryKey = 'id_pesan';
    public $incrementing = false; // Karena id_pesan kita isi manual (1001, dst)
    protected $fillable = [
        'id_pesan',
        'id_pelanggan',
        'tgl_pesan'
    ];

    public function pelanggan() //Relasi pesan -> pelanggan
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
        //Pelanggan::class = Nama Model tujuan
        //'id_pelanggan' (Parameter ke-2) = Ini adalah nama kolom Foreign Key yang ada di tabel saat ini (pesan).
        //'id_pelanggan' (Parameter ke-3) = Ini adalah nama kolom Primary Key yang ada di tabel tujuan (pelanggan).
    }

    public function detilPesan() // Relasi ke DetilPesan
    {
        return $this->hasMany(DetilPesan::class, 'id_pesan', 'id_pesan');
    }
}


