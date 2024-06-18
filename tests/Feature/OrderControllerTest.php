<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to store a new order.
     * This test verifies that a client can create a new order and checks that the order is saved in the database.
     */
    public function testStoreOrder()
    {
        // Create a client user
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        // Make a request to create a new order with the client's token and order details
        $response = $this->actingAs($client, 'api')
            ->post('/api/order', [
                'token' => $client->token,
                'pickup_name' => 'John Doe',
                'pickup_mobile' => '1234567890',
                'pickup_address' => '123 Main St',
                'pickup_lat' => 40.7128,
                'pickup_long' => -74.0060,
                'delivery_name' => 'Jane Doe',
                'delivery_mobile' => '0987654321',
                'delivery_address' => '456 Elm St',
                'delivery_lat' => 40.7128,
                'delivery_long' => -74.0060,
            ]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the response contains a service_request_id in the JSON structure
        $response->assertJsonStructure(['service_request_id']);
        // Assert that the orders table has the new order record with the client's ID
        $this->assertDatabaseHas('orders', ['client_id' => $client->id]);
    }

    /**
     * Test to cancel an existing order.
     * This test verifies that a client can cancel their pending order and checks that the order status is updated to cancelled in the database.
     */
    public function testCancelOrder()
    {
        // Create a client user
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        // Create a pending order for the client
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'client_id' => $client->id
        ]);

        // Make a request to cancel the order with the client's token
        $response = $this->actingAs($client, 'api')
            ->put("/api/order/{$order->id}/cancel", [
                'token' => $client->token
            ]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the orders table has the order record with the status updated to cancelled
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => Order::STATUS_CANCELLED]);
    }
}
