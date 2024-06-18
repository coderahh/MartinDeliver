<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'pickup_name', 'pickup_mobile', 'pickup_address', 'pickup_lat', 'pickup_long',
        'delivery_name', 'delivery_mobile', 'delivery_address', 'delivery_lat', 'delivery_long', 'status',
    ];

    // Order status constants
    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_PICKED_UP = 3;
    const STATUS_DELIVERED = 4;

    /**
     * Define a relationship with the User model representing the client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
