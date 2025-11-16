<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     operationId="getCategoriesList",
     *     tags={"Categories"},
     *     summary="Get list of categories",
     *     description="Returns a list of all categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             ),
     *             @OA\Property(property="message", type="string", example="Kategoriler başarıyla listelendi.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->service->getAllCategories();
        return response()->success($categories, 'Kategoriler başarıyla listelendi.');
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     operationId="storeCategory",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Create a new category with name and optional description",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic items and gadgets")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category"),
     *             @OA\Property(property="message", type="string", example="Kategori başarıyla oluşturuldu.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string'
        ]);

        $category = $this->service->createCategory($data);
        return response()->success($category, 'Kategori başarıyla oluşturuldu.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     operationId="showCategory",
     *     tags={"Categories"},
     *     summary="Get a specific category",
     *     description="Returns a specific category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category"),
     *             @OA\Property(property="message", type="string", example="Kategori başarıyla getirildi.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function show(string $id)
    {
        $category = $this->service->getCategoryById($id);
        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->success($category, 'Kategori başarıyla getirildi.');
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     description="Update a specific category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated category data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Updated Category"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category"),
     *             @OA\Property(property="message", type="string", example="Kategori başarıyla güncellendi.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string'
        ]);

        $category = $this->service->updateCategory($id, $data);
        if (! $category) {
            return response()->error('Kategori bulunamadı veya güncellenemedi', $category, 404);
        }
        return response()->success($category, 'Kategori başarıyla güncellendi.');
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     description="Delete a specific category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategori başarıyla silindi.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $deleted = $this->service->deleteCategory($id);
        if (! $deleted) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->success($id, 'Kategori başarıyla silindi.');
    }
}
