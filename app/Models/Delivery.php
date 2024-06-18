<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'courier_id', 'status', 'lat', 'long',
    ];

    // Delivery status constants
    const STATUS_ACCEPTED = 0;
    const STATUS_CANCELLED = 1;
    const STATUS_PICKED_UP = 2;
    const STATUS_EN_ROUTE = 3;
    const STATUS_DELIVERED = 4;

    /**
     * Define a relationship with the Order model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Define a relationship with the User (Courier) model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }
}
