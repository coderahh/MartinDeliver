<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Models\User;

class OrderController extends Controller
{
    /**
     * Store a new order.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'token' => 'required|exists:users,token',
            'pickup_name' => 'required|string',
            'pickup_mobile' => 'required|string',
            'pickup_address' => 'required|string',
            'pickup_lat' => 'required|numeric',
            'pickup_long' => 'required|numeric',
            'delivery_name' => 'required|string',
            'delivery_mobile' => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_lat' => 'required|numeric',
            'delivery_long' => 'required|numeric',
        ]);

        // Check if the user exists and is a client
        $user = $this->checkUser($request->token, User::ROLE_CLIENT);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prepare order data and associate it with the client
        $orderData = $request->all();
        $orderData['client_id'] = $user->id;

        // Create the order record
        $order = Order::create($orderData);

        return response()->json(['service_request_id' => $order->id], 200);
    }

    /**
     * Cancel an existing order.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        // Validate the request parameters
        $request->validate([
            'token' => 'required|exists:users,token'
        ]);

        // Check if the user exists and is a client
        $user = $this->checkUser($request->token, User::ROLE_CLIENT);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the order to cancel
        $order = Order::findOrFail($id);

        // Verify authorization to cancel the order
        if (!($user->id === $order->client_id &&
            ($order->status === Order::STATUS_PENDING || $order->status === Order::STATUS_ACCEPTED))) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update order status to cancelled
        $order->status = Order::STATUS_CANCELLED;
        $order->save();

        // If there is a corresponding delivery, update its status as well
        $delivery = Delivery::find($order->id);
        if ($delivery) {
            $delivery->status = Delivery::STATUS_CANCELLED;
            $delivery->save();
        }

        return response()->json(['message' => 'Order cancelled.'], 200);
    }

    /**
     * Helper function to check if the user exists and has a specific role.
     *
     * @param  string  $token
     * @param  string  $role
     * @return User|null
     */
    public function checkUser($token, $role)
    {
        return User::where([['token', $token], ['role', $role]])->first();
    }
}
