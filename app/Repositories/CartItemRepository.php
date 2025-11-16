<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\Eloquent\BaseRepository;

class CartItemRepository extends BaseRepository
{
    public function __construct(CartItem $model)
    {
        parent::__construct($model);
    }

    public function addProductToCart($userId, $productId, $quantity, $cartId)
    {
        // Aynı ürünün sepette olup olmadığını kontrol et
        $existingCartItem = $this->model
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            // Ürün varsa miktarı artır
            $existingCartItem->quantity += $quantity;
            $existingCartItem->save();
            return $existingCartItem;
        }

        // Ürün yoksa yeni kayıt oluştur
        return $this->model->create([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function deleteAllCartItemsByCartId($cartId)
    {
        return $this->model->where('cart_id', $cartId)->delete();
    }
}
