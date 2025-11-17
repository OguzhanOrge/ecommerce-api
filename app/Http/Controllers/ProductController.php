<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    /**
     * @OA\Tag(
     *     name="Products",
     *     description="Product management"
     * )
     */
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;

        $this->middleware(['jwt.auth', 'role:admin'])->only([
            'store',
            'update',
            'destroy'
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="List products",
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Search term for name or description"),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="in_stock", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"price_asc","price_desc","newest"})),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="with", in="query", @OA\Schema(type="string"), description="Comma separated relations to eager load"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
            'category_id',
            'search',
            'min_price',
            'max_price',
            'in_stock',
            'sort',
            'page'
        ]);

            if ($request->has('in_stock')) {
                $filters['in_stock'] = $request->boolean('in_stock');
            }

            $perPage = (int) $request->query('per_page', 20);

            $with = [];
            if ($request->has('with')) {
                $with = array_filter(array_map('trim', explode(',', $request->query('with'))));
            }

            $products = $this->service->getAllProducts($filters, $perPage, $with);

            return response()->success($products, 'Ürünler başarıyla listelendi.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => $th->getMessage()], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create a product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock","category_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="stock_quantity", type="integer"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'sometimes|string'
        ]);
        try {
            $product = $this->service->createProduct($data);
            return response()->success($product, 'Ürün başarıyla oluşturuldu.', 201);
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Ürün oluşturulurken bir hata oluştu'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get product by id",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id)
    {
        try {
            $product = $this->service->getProductById($id);
            if (! $product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            return response()->success($product, 'Ürün başarıyla getirildi.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Ürün getirilirken bir hata oluştu'], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="stock", type="integer"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|min:3|max:255',
            'price' => 'sometimes|required|numeric|min:0.01',
            'stock' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'description' => 'sometimes|string'
        ]);
        try {
            $product = $this->service->updateProduct($id, $data);
            if (! $product) {
                return response()->error('Ürün bulunamadı veya güncellenemedi', $product, 404);
            }
            return response()->success($product, 'Ürün başarıyla güncellendi.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Ürün güncellenirken bir hata oluştu'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->service->deleteProduct($id);
            if (! $deleted) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            return response()->success($id, 'Ürün başarıyla silindi.');
        } catch (\Throwable $th) {
            return response()->error(['success' => false, 'message' => 'Ürün silinirken bir hata oluştu'], 500);
        }

    }
}
