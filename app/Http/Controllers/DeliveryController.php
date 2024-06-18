<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DeliveryController extends Controller
{
    /**
     * Get available orders for a courier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableOrders(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'token' => 'required|exists:users,token'
        ]);

        // Check if the user exists and is a courier
        $user = User::where([['token', $request->token], ['role', User::ROLE_COURIER]])->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Fetch pending orders
        $orders = Order::where('status', Order::STATUS_PENDING)->get();
        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Accept an order by a courier.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptOrder(Request $request, $id)
    {
        // Validate the request parameters
        $request->validate([
            'token' => 'required|exists:users,token',
            'lat' => 'required_with:long|numeric',
            'long' => 'required_with:lat|numeric',
        ]);

        // Check if the user exists and is a courier
        $user = $this->checkUser($request->token, User::ROLE_COURIER);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Perform the order acceptance within a transaction
        try {
            DB::transaction(function () use ($request, $id, $user) {
                $order = Order::lockForUpdate()->findOrFail($id);

                // Check if the order is pending
                if ($order->status != Order::STATUS_PENDING) {
                    return response()->json(['message' => 'Order already accepted by another courier'], 403);
                }

                // Create a new delivery record
                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'courier_id' => $user->id,
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

                // Update the order status to accepted
                $order->status = Order::STATUS_ACCEPTED;
                $order->save();

                return response()->json(['delivery' => $delivery], 200);
            });

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update delivery status by a courier.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Validate the request parameters
        $request->validate([
            'token' => 'required|exists:users,token',
            'status' => 'required|in:' . implode(',', [
                Delivery::STATUS_PICKED_UP,
                Delivery::STATUS_EN_ROUTE,
                Delivery::STATUS_DELIVERED,
            ]),
            'lat' => 'required_with:long|numeric',
            'long' => 'required_with:lat|numeric',
        ]);

        // Check if the user exists and is a courier
        $user = $this->checkUser($request->token, User::ROLE_COURIER);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the delivery record
        $delivery = Delivery::findOrFail($id);

        // Check authorization to update delivery
        if (!($user->id === $delivery->courier_id && $delivery->status != Delivery::STATUS_CANCELLED)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update delivery status and location
        $delivery->status = $request->status;
        $delivery->lat = $request->lat;
        $delivery->long = $request->long;
        $delivery->save();

        // Update corresponding order status if delivered or picked up
        if (
            $delivery->status === Delivery::STATUS_DELIVERED ||
            $delivery->status === Delivery::STATUS_PICKED_UP
        ) {
            $delivery->order->status = $delivery->status === Delivery::STATUS_DELIVERED ?
                Order::STATUS_DELIVERED : Order::STATUS_PICKED_UP;
            $delivery->order->save();
        }

        return response()->json(['delivery_id' => $delivery->id], 200);
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
