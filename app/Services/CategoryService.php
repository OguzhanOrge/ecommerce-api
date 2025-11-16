<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    protected CategoryRepository $category;

    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    /**
     * Get all categories
     */
    public function getAllCategories()
    {
        return $this->category->all();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id)
    {
        try {
            return $this->category->find($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->category->create($data);
        });
    }

    /**
     * Update category
     */
    public function updateCategory($id, array $data)
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                return $this->category->update($id, $data);
            });
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                return $this->category->delete($id);
            });
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
