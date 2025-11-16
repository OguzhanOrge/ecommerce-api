<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Eloquent\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

}
