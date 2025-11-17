<?php

namespace App\Repositories;
use App\Models\Product;
use App\Models\Category;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Product-specific filter override.
     * Supports all BaseRepository filters plus:
     * - category_slug => string
     * - in_stock => bool
     * - sort => 'price_asc'|'price_desc'|'newest'
     */
    public function filter(array $filters = [], array $with = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with($with);
        $page = $filters['page'] ?? null;
        unset($filters['page']);
        // category_slug -> resolve to category_id(s)
        if (isset($filters['category_slug']) && $filters['category_slug'] !== '') {
            $slug = $filters['category_slug'];
            $categoryIds = Category::where('slug', $slug)->pluck('id')->toArray();
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            } else {
                // No categories match -- return empty paginator
                return $this->model->newQuery()->whereRaw('1 = 0')->paginate($perPage);
            }
            unset($filters['category_slug']);
        }

        // in_stock filter
        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->where('stock_quantity', '>', 0);
            }
            unset($filters['in_stock']);
        }

        // Search handling (name, description)
        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            unset($filters['search']);
        }

        // Price range handling
        $hasMin = isset($filters['min_price']) && $filters['min_price'] !== '';
        $hasMax = isset($filters['max_price']) && $filters['max_price'] !== '';
        if ($hasMin || $hasMax) {
            if ($hasMin && $hasMax) {
                $query->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
            } elseif ($hasMin) {
                $query->where('price', '>=', $filters['min_price']);
            } else {
                $query->where('price', '<=', $filters['max_price']);
            }
            unset($filters['min_price'], $filters['max_price']);
        }

        // Remaining generic filters
        foreach ($filters as $key => $value) {
            if (is_null($value) || $value === '') continue;

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, 'like', "%{$value}%");
            }
        }

        // Sorting
        if (isset($filters['sort'])) {
            $sort = $filters['sort'];
            if ($sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($sort === 'newest') {
                $query->orderBy('created_at', 'desc');
            }
            // Remove sort so it doesn't get treated by generic filters
            // (it was not part of $filters iteration because we check after, but keep for safety)
        }



        return $query->paginate($perPage,['*'],'page',$page);
    }

}
