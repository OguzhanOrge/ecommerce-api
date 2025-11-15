<?php

namespace App\Http\Controllers;
use App\Http\Requests\testreq;
use Illuminate\Http\Request;
use App\Models\User;

class TestController extends Controller
{
        /**
     * @OA\Get(
     *      path="/api/test/{id}",
     *      operationId="getTest",
     *      tags={"Test"},
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="User ID",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      summary="Get list of test",
     *      description="Returns list of test",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *      )
     * )
     */
    public function index(testreq $request)
    {
        $request->validated();

        return response()->success(User::all(), 'Kullanıcılar başarıyla listelendi.');
    }
}
