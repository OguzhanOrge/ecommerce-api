<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Eloquent\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get a single order with its items and product details.
     *
     * @param int $orderId
     * @return \App\Models\Order
     */
    public function getOrderDetailsById(int $orderId)
    {
        return $this->model->with(['orderItems.product'])->findOrFail($orderId);
    }

    /**
     * Get all orders for a given user with order items and product relation.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['orderItems' => function ($query) {
                $query->with('product');
            }])
            ->get();
    }

}
