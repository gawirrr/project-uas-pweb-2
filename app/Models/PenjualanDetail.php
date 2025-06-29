<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;
    protected $fillable = ['penjualan_id', 'barang_id', 'jumlah', 'harga', 'subtotal'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}