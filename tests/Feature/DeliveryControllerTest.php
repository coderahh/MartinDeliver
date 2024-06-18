<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to get available orders for a courier.
     * This test creates 5 orders and verifies that the available orders endpoint
     * returns these orders correctly for a courier user.
     */
    public function testGetAvailableOrders()
    {
        // Create 5 orders
        $orders = Order::factory()->count(5)->create();
        // Create a courier user
        $courier = User::factory()->create(['role' => User::ROLE_COURIER]);

        // Make a request to get available orders with the courier's token
        $response = $this->actingAs($courier, 'api')
            ->post('/api/delivery/orders', ['token' => $courier->token]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the response contains 5 orders
        $response->assertJsonCount(5, 'orders');
    }

    /**
     * Test for a courier to accept an order.
     * This test verifies that a courier can accept a pending order and updates the order and delivery statuses accordingly.
     */
    public function testAcceptOrder()
    {
        // Create a courier user
        $courier = User::factory()->create(['role' => User::ROLE_COURIER]);
        // Create a pending order
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        // Make a request to accept the order with the courier's token and location
        $response = $this->actingAs($courier, 'api')
            ->post("/api/delivery/{$order->id}/accept", [
                'token' => $courier->token,
                'lat' => 40.7128,
                'long' => -74.0060
            ]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the deliveries table has the new delivery record
        $this->assertDatabaseHas('deliveries', ['order_id' => $order->id, 'courier_id' => $courier->id]);
        // Assert that the orders table has updated the order status to accepted
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => Order::STATUS_ACCEPTED]);
    }

    /**
     * Test to update the status of a delivery.
     * This test verifies that a courier can update the status of their delivery
     * and that the corresponding order status is updated if applicable.
     */
    public function testUpdateStatus()
    {
        // Create a courier user
        $courier = User::factory()->create(['role' => User::ROLE_COURIER]);
        // Create an accepted order
        $order = Order::factory()->create(['status' => Order::STATUS_ACCEPTED]);
        // Create a delivery for the order by the courier
        $delivery = Delivery::factory()->create([
            'status' => Delivery::STATUS_ACCEPTED,
            'order_id' => $order->id,
            'courier_id'=> $courier->id
        ]);

        // Make a request to update the delivery status with the courier's token and location
        $response = $this->actingAs($courier, 'api')
            ->put("/api/delivery/{$delivery->id}/status", [
                'token' => $courier->token,
                'status' => Delivery::STATUS_PICKED_UP,
                'lat' => 40.7128,
                'long' => -74.0060
            ]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the deliveries table has updated the delivery status to picked up
        $this->assertDatabaseHas('deliveries', ['id' => $delivery->id, 'status' => Delivery::STATUS_PICKED_UP]);
        // Assert that the orders table has updated the order status to picked up
        $this->assertDatabaseHas('orders', ['id' => $delivery->order_id, 'status' => Order::STATUS_PICKED_UP]);
    }
}
