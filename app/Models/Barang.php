<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'nama',
        'id_kategori',
        'harga',
        'stok',
    ];

    public function stokMasuk() { return $this->hasMany(StokMasuk::class); }
    public function stokKeluar() { return $this->hasMany(StokKeluar::class); }
    public function penjualan() { return $this->hasMany(Penjualan::class); }
    public function kategori() { return $this->belongsTo(Kategori::class, 'id_kategori'); }
}


