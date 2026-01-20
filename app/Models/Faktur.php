<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faktur extends Model
{
    use HasFactory;

    protected $table = 'faktur';
    protected $primaryKey = 'id_faktur';
    public $incrementing = false; // Karena ID diisi manual (2001, 2002, dst)

    protected $fillable = [
        'id_faktur',
        'id_pesan',
        'tgl_faktur'
    ];

    // Relasi: Satu faktur merujuk ke satu pesanan
    public function pesan()
    {
        return $this->belongsTo(Pesan::class, 'id_pesan', 'id_pesan');
    }
}
