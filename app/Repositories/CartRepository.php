<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Repositories\Eloquent\BaseRepository;

class CartRepository extends BaseRepository
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function getCartByUserId($userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['cartItems' => function ($query) {
                $query->with('product');
            }])
            ->first();
    }

    public function createCart($userId)
    {
        return $this->model->create([
            'user_id' => $userId,
        ]);
    }

}
