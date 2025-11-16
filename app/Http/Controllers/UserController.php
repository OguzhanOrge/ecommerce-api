<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Operations about users"
 * )
 */
class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of users",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function index()
    {
        $users = $this->service->getAllUsers();
        return response()->success($users, 'Kullanıcılar başarıyla listelendi.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string'
        ]);

        $user = $this->service->createUser($data);
        return response()->success($user, 'Kullanıcı başarıyla oluşturuldu.', 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Get user by id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show(string $id)
    {
        $user = $this->service->getUserById($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->success($user, 'Kullanıcı başarıyla getirildi.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update an existing user",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|unique:users,email,{$id}",
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string'
        ]);

        $user = $this->service->updateUser($id, $data);
        if (! $user) {
            return response()->error('Kullanıcı bulunamadı veya güncellenemedi', $user, 404);
        }
        return response()->success(null, 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy(string $id)
    {
        $deleted = $this->service->deleteUser($id);
        if (! $deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->success($id, 'Kullanıcı başarıyla silindi.');
    }
}
