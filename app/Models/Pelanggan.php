<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_pelanggan',
        'nm_pelanggan',
        'alamat',
        'telepon',
        'email'
    ];

    public function pesan() //relasi pelanggan -> pesan
    {
        return $this->hasMany(Pesan::class, 'id_pelanggan', 'id_pelanggan');
    }
}


