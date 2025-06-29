<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_invoice',
        'pelanggan',
        'total_harga',
    ];

    /**
     * Get the details for the sale.
     */
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}