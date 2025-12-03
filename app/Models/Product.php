<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'nama_produk',
        'deskripsi',
        'kategori',
        'image_url',
        'stock',
        'harga_modal',
        'persen_keuntungan',
        'harga_jual',
        'is_available'
    ];

    protected static function booted()
    {
        // Saat Menyimpan: Otomatis isi user_id dengan ID user yang login
        static::creating(function ($product) {
            if (Auth::check()) {
                $product->user_id = Auth::id();
            }
        });

        // Saat Mengambil Data: Otomatis filter hanya data milik user yang login
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }
}
