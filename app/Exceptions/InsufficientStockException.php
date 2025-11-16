<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public $product;

    public function __construct($product)
    {
        parent::__construct("Stok yetersiz");
        $this->product = $product;
    }
}
