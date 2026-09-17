<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = ['code', 'customer_name', 'phone', 'email', 'address', 'items', 'subtotal', 'shipping_fee', 'discount', 'total', 'currency', 'status', 'payment_method', 'payment_status', 'customer_note', 'internal_note', 'version', 'history', 'created_by'];

    protected function casts(): array
    {
        return ['items' => 'array', 'history' => 'array', 'subtotal' => 'integer', 'shipping_fee' => 'integer', 'discount' => 'integer', 'total' => 'integer', 'version' => 'integer'];
    }
}
