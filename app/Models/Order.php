<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('jumlah', 'harga_satuan')
            ->withTimestamps();
    }
}
