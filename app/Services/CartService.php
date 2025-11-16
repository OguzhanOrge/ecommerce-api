<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\CartRepository;
use App\Repositories\CartItemRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{
    protected CartRepository $cart;
    protected CartItemRepository $cartItem;

    public function __construct(CartRepository $cart,CartItemRepository $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    /**
     * Get all carts
     */
    public function getAllCartByUserId($userId)
    {
        return $this->cart->getCartByUserId($userId);
    }

    /**
     * Get cart by ID
     */
    public function getCartById($id)
    {
        try {
            return $this->cart->find($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Add product to cart (creates cart if doesn't exist)
     */
    public function addProductToCart(int $userId, int $productId, int $quantity)
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            // Kullanıcının sepeti varsa getir, yoksa oluştur
            $cart = $this->cart->getCartByUserId($userId);

            if (!$cart) {
                $cart = $this->cart->createCart($userId);
            }

            // Ürünü sepete ekle
            return $this->cartItem->addProductToCart($userId, $productId, $quantity,$cart->id);
        });
    }

    /**
     * Update product quantity in cart
     */
    public function updateProductCount($userId, $productId, $quantity)
    {
        try {
            return DB::transaction(function () use ($userId, $productId, $quantity) {
                $cart = $this->cart->getCartByUserId($userId);

                if (!$cart) {
                    return null;
                }

                // CartItem'ı product_id'ye göre bul
                $cartItem = $cart->cartItems->firstWhere('product_id', $productId);

                if (!$cartItem) {
                    return null;
                }

                // Miktar güncelle
                return $this->cartItem->update($cartItem->id, ['quantity' => $quantity]);
            });
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Delete cart
     */
    public function removeProduct($userId, $productId)
    {
        try {
            return DB::transaction(function () use ($userId, $productId) {
                $cart = $this->cart->getCartByUserId($userId);

                if (!$cart) {
                    return false;
                }

                // CartItem'ı product_id'ye göre bul
                $cartItem = $cart->cartItems->firstWhere('product_id', $productId);

                if (!$cartItem) {
                    return false;
                }

                return $this->cartItem->delete($cartItem->id);
            });
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function clearCart($userId)
    {
        try {
            return DB::transaction(function () use ($userId) {
                $cart = $this->cart->getCartByUserId($userId);
                if (!$cart) {
                    return false;
                }
                return $this->cartItem->deleteAllCartItemsByCartId($cart->id);
            });
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
