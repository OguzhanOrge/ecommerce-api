<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Operations related to shopping cart"
 * )
 */
class CartController extends Controller
{
    protected CartService $service;

    public function __construct(CartService $service)
    {
        $this->service = $service;
    }
    /**
     * GET /api/cart
     * View current user's cart
        *
        * @OA\Get(
        *     path="/api/cart",
        *     summary="View current user's cart",
        *     tags={"Cart"},
        *     security={{"bearerAuth":{}}},
        *     @OA\Response(
        *         response=200,
        *         description="Cart retrieved",
        *         @OA\JsonContent(type="object")
        *     ),
        *     @OA\Response(response=401, description="Unauthorized")
        * )
     */
    public function index()
    {
        $userId = auth('api')->id();
        $cart = $this->service->getAllCartByUserId($userId);
        if (! $cart) {
            return response()->success([], 'Sepet bulunamadı.');
        }
        return response()->success($cart, 'Sepet başarıyla getirildi.');
    }

    /**
     * POST /api/cart/add
     * Add product to cart
        *
        * @OA\Post(
        *     path="/api/cart/add",
        *     summary="Add product to cart",
        *     tags={"Cart"},
        *     security={{"bearerAuth":{}}},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"product_id","quantity"},
        *             @OA\Property(property="product_id", type="integer", example=1),
        *             @OA\Property(property="quantity", type="integer", example=2)
        *         )
        *     ),
        *     @OA\Response(response=201, description="Product added", @OA\JsonContent(type="object")),
        *     @OA\Response(response=400, description="Validation error"),
        *     @OA\Response(response=401, description="Unauthorized")
        * )
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = auth('api')->id();
        $item = $this->service->addProductToCart($userId, $data['product_id'], $data['quantity']);
        return response()->success($item, 'Ürün sepete eklendi.', 201);
    }

    /**
     * PUT /api/cart/update
     * Update product quantity in cart
        *
        * @OA\Put(
        *     path="/api/cart/update",
        *     summary="Update product quantity in cart",
        *     tags={"Cart"},
        *     security={{"bearerAuth":{}}},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"product_id","quantity"},
        *             @OA\Property(property="product_id", type="integer", example=1),
        *             @OA\Property(property="quantity", type="integer", example=3)
        *         )
        *     ),
        *     @OA\Response(response=200, description="Updated", @OA\JsonContent(type="object")),
        *     @OA\Response(response=404, description="Not found"),
        *     @OA\Response(response=401, description="Unauthorized")
        * )
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = auth('api')->id();
        $updated = $this->service->updateProductCount($userId, $data['product_id'], $data['quantity']);
        if (! $updated) {
            return response()->error('Güncelleme başarısız veya ürün bulunamadı.', null, 404);
        }
        return response()->success($updated, 'Sepet ürünü başarıyla güncellendi.');
    }

    /**
     * DELETE /api/cart/remove/{product_id}
     * Remove a product from cart
        *
        * @OA\Delete(
        *     path="/api/cart/remove/{product_id}",
        *     summary="Remove a product from cart",
        *     tags={"Cart"},
        *     security={{"bearerAuth":{}}},
        *     @OA\Parameter(
        *         name="product_id",
        *         in="path",
        *         required=true,
        *         @OA\Schema(type="integer")
        *     ),
        *     @OA\Response(response=200, description="Removed", @OA\JsonContent(type="object")),
        *     @OA\Response(response=404, description="Not found"),
        *     @OA\Response(response=401, description="Unauthorized")
        * )
     */
    public function remove(string $productId)
    {
        $userId = auth('api')->id();
        $removed = $this->service->removeProduct($userId, $productId);
        if (! $removed) {
            return response()->error('Ürün bulunamadı veya silinemedi.', null, 404);
        }
        return response()->success(['product_id' => $productId], 'Ürün sepetten çıkarıldı.');
    }

    /**
     * DELETE /api/cart/clear
     * Clear the current user's cart
        *
        * @OA\Delete(
        *     path="/api/cart/clear",
        *     summary="Clear the current user's cart",
        *     tags={"Cart"},
        *     security={{"bearerAuth":{}}},
        *     @OA\Response(response=200, description="Cleared"),
        *     @OA\Response(response=500, description="Server error"),
        *     @OA\Response(response=401, description="Unauthorized")
        * )
     */
    public function clear()
    {
        $userId = auth('api')->id();
        $cleared = $this->service->clearCart($userId);
        if (! $cleared) {
            return response()->error('Sepet temizlenemedi.', null, 500);
        }
        return response()->success(null, 'Sepet başarıyla temizlendi.');
    }
}
