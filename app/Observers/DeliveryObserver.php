<?php

namespace App\Observers;

use App\Models\Delivery;
use Illuminate\Support\Facades\Http;

class DeliveryObserver
{
    /**
     * Handle the Delivery "created" event.
     *
     * @param  Delivery  $delivery
     * @return void
     */
    public function created(Delivery $delivery): void
    {
        // Retrieve the associated order and webhook URL of the client
        $order = $delivery->order;
        $webhookUrl = $order->client->webhook_url;
        $courier = $delivery->courier;

        // If a webhook URL is provided for the client, send a POST request to it
        if ($webhookUrl) {
            Http::post($webhookUrl, [
                'order_id' => $order->id,
                'delivery_id' => $delivery->id,
                'status' => $delivery->status,
                'courier_name' => $courier->name,
                'courier_mobile' => $courier->mobile,
                'courier_location' => [
                    'latitude' => $delivery->lat,
                    'longitude' => $delivery->long,
                ],
            ]);
        }
    }

    /**
     * Handle the Delivery "updated" event.
     *
     * @param  Delivery  $delivery
     * @return void
     */
    public function updated(Delivery $delivery): void
    {
        // Retrieve the associated order and webhook URL of the client
        $order = $delivery->order;
        $webhookUrl = $order->client->webhook_url;
        $courier = $delivery->courier;

        // If a webhook URL is provided for the client, send a POST request to it
        if ($webhookUrl) {
            Http::post($webhookUrl, [
                'order_id' => $order->id,
                'delivery_id' => $delivery->id,
                'status' => $delivery->status,
                'courier_name' => $courier->name,
                'courier_mobile' => $courier->mobile,
                'courier_location' => [
                    'latitude' => $delivery->lat,
                    'longitude' => $delivery->long,
                ],
            ]);
        }
    }
}
