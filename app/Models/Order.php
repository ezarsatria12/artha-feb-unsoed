<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'nama_pemesan',
        'order_date',
        'tipe_pesanan',
        'payment_method',
        'total_uang_masuk',
        'total_modal',
        'total_profit',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // THE MAGIC: Global Scope & Auto Fill
    protected static function booted()
    {
        static::creating(function ($order) {
            if (Auth::check()) {
                $order->user_id = Auth::id();
            }
        });

        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }
}
