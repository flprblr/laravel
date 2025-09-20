<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusUpdate extends Model
{
    protected $fillable = [
        'ecommerce', 'order_number', 'status', 'attempts', 'last_error', 'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime:d/m/Y H:i:s',
    ];
}
