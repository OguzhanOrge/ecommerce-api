<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }
    /**
     * Get authenticated user's orders
     *
     * @OA\Get(
     *     path="/api/orders",
     *     operationId="getOrders",
     *     tags={"Orders"},
     *     summary="Get authenticated user's orders",
     *     description="Returns orders (with items and product details) for the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=4),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-16T11:51:58.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-16T11:51:58.000000Z"),
     *                     @OA\Property(
     *                         property="cart_items",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=12),
     *                             @OA\Property(property="cart_id", type="integer", example=4),
     *                             @OA\Property(property="product_id", type="integer", example=4),
     *                             @OA\Property(property="quantity", type="integer", example=2),
     *                             @OA\Property(
     *                                 property="product",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=4),
     *                                 @OA\Property(property="name", type="string", example="string"),
     *                                 @OA\Property(property="description", type="string", example="string"),
     *                                 @OA\Property(property="price", type="string", example="15.00"),
     *                                 @OA\Property(property="stock_quantity", type="integer", example=11)
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No orders found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No orders found"))
     *     )
     * )
     */
    public function index()
    {
        try {
            $userId = auth('api')->id();
            return response()->success($this->service->getOrderByUserId($userId));
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Siparişler getirilirken bir hata oluştu'], 500);
        }
    }
    /**
     * Create a new order from user's cart
     *
     * @OA\Post(
     *     path="/api/orders",
     *     operationId="makeOrder",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Creates a new order from the authenticated user's cart. Requires JWT authentication.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of cart items that were converted to order items",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="cart_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=5),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", format="float", example=49.99),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-16T10:30:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-16T10:30:00Z")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order created successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid JWT token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User's cart not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No query results found for model")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function makeOrder(Request $request)
    {
        try {
            $userId = auth('api')->id();
            $order = $this->service->createOrder($userId);
            return response()->success($order, 'Order created successfully.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Sipariş oluşturulurken bir hata oluştu'], 500);
        }
    }

    /**
     * Get order details by id
     *
     * @OA\Get(
     *     path="/api/orders/{orderId}",
     *     operationId="getOrderById",
     *     tags={"Orders"},
     *     summary="Get order details by id",
     *     description="Returns a single order with its items and product details.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         description="ID of order to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details retrieved",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=4),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-16T11:51:58.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-16T11:51:58.000000Z"),
     *                 @OA\Property(
     *                     property="cart_items",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=12),
     *                         @OA\Property(property="cart_id", type="integer", example=4),
     *                         @OA\Property(property="product_id", type="integer", example=4),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(
     *                             property="product",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=4),
     *                             @OA\Property(property="name", type="string", example="string"),
     *                             @OA\Property(property="description", type="string", example="string"),
     *                             @OA\Property(property="price", type="string", example="15.00"),
     *                             @OA\Property(property="stock_quantity", type="integer", example=11)
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Order not found"))
     *     )
     * )
     */
    public function show($orderId)
    {
        try {
            $order = $this->service->getOrderDetailsById($orderId);
            if (! $order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            return response()->success($order);
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Sipariş değerlerinde bir hata oluştu'], 404);
        }

    }
    /**
     * Get order details by id
     *
     * @OA\Put(
 *     path="/api/orders/{orderId}",
 *     operationId="updateOrderStatus",
 *     tags={"Orders"},
 *     summary="Update order status by ID,status is type of enum('pending', 'paid', 'shipped', 'completed', 'cancelled')",
 *     description="Updates the status of a specific order.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="orderId",
 *         in="path",
 *         description="ID of the order to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="shipped"),
 *             required={"status"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     )
 * )
 */
    public function update(Request $request, $orderId)
    {
        try {
            $updatedOrder = $this->service->updateOrderStatus($orderId, $request->input('status'));
            if (! $updatedOrder) {
                return response()->error(['success' => false, 'message' => 'Order not found'], 404);
            }
            return response()->success($updatedOrder, 'Order status updated successfully.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => '(status) değeri geçersizdir.'], 500);
        }


    }
}
