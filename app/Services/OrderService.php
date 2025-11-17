<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Repositories\CartItemRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    protected OrderRepository $order;

    protected CartRepository $cart;
    protected CartItemRepository $cartItem;

    public function __construct(OrderRepository $order, CartRepository $cart, CartItemRepository $cartItem)
    {
        $this->order = $order;
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }
    public function createOrder(int $userId)
    {
        return DB::transaction(function () use ($userId) {
            $cart = $this->cart->getCartByUserId($userId);
            if (! $cart) {
                throw new ModelNotFoundException('Cart not found for user: ' . $userId);
            }

            // CartItems are eager-loaded with product in CartRepository::getCartByUserId
            $cartItems = $cart->cartItems;

            // If cart has no items, return an empty cart structure
            if ($cartItems->isEmpty()) {
                abort(400,'Sepette sipariş oluşturmak için ürün bulunmamaktadır.'); // No items in cart
            }

            // Check if all products have sufficient stock before creating order
            foreach ($cartItems as $item) {
                if ($item->product->stock_quantity < $item->quantity) {
                    abort(400,'Üründen yeterli stok bulunmamaktadır.'); // Insufficient stock
                }
            }

            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $unitPrice = isset($item->product->price) ? (float) $item->product->price : 0.0;
                $totalPrice += $unitPrice * $item->quantity;
            }

            // Create order record
            $order = $this->order->create([
                'user_id' => $userId,
                'total_amount' => $totalPrice,
                'status' => 'pending',
            ]);

            // Create order items (store unit price) and reduce product stock
            foreach ($cartItems as $item) {
                $unitPrice = isset($item->product->price) ? (float) $item->product->price : 0.0;
                $order->orderItems()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $unitPrice,
                ]);

                // Reduce product stock by order quantity
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Prepare response structure similar to the sample payload
            $response = [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'cart_items' => $cartItems->toArray(),
            ];

            // Optionally clear the cart
            $this->cartItem->deleteAllCartItemsByCartId($cart->id);

            return $response;
        });
    }

    public function getOrderByUserId($userId)
    {
        // Return all orders for the user with their items and product details
        return $this->order->getOrdersByUserId($userId);
    }

    public function getOrderDetailsById($orderId)
    {
        try {
            return $this->order->getOrderDetailsById($orderId);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = $this->order->update($orderId,['status' => $status]);

        return $order;
    }
}
