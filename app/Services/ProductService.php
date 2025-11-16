<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    protected ProductRepository $product;

    public function __construct(ProductRepository $product)
    {
        $this->product = $product;
    }

    /**
     * Get all products with optional filters:
     * - pagination: $perPage
     * - category filter: 'category_id' => int|array
     * - price range: 'min_price' and/or 'max_price'
     * - search: 'search' => string (searches name and description)
     */
    public function getAllProducts(array $filters = [], int $perPage = 15, array $with = [])
    {
        return $this->product->filter($filters, $with, $perPage);
    }

    /**
     * Get product by ID
     */
    public function getProductById($id)
    {
        try {
            return $this->product->find($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->product->create($data);
        });
    }

    /**
     * Update product
     */
    public function updateProduct($id, array $data)
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                return $this->product->update($id, $data);
            });
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                return $this->product->delete($id);
            });
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
