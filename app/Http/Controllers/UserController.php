<?php

namespace App\Http\Controllers;

use App\Http\Requests\user\UpdateUserRequest;
use App\Traits\Filterable;
use App\Http\Requests\user\IndexUserRequest;
use App\Http\Requests\user\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    use Filterable;

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/user",
     *     tags={"User"},
     *     summary="Get list of users",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="names", in="query", description="Filter by names", required=false, @OA\Schema(type="string") ),
     *     @OA\Parameter(name="typeuser_id", in="query", description="Filter by type user id", required=false, @OA\Schema(type="integer") ),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer") ),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer") ),
     *     @OA\Parameter(name="all", in="query", description="Get all items", required=false, @OA\Schema(type="boolean") ),
     *     @OA\Response(response=200, description="List of users", @OA\JsonContent( type="array", @OA\Items(ref="#/components/schemas/User") ) ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent( ref="#/components/schemas/Unauthenticated" ) ),
     * )
     */
    public function index(IndexUserRequest $request)
    {
        return $this->getFilteredResults(
            User::class,
            $request,
            User::filters,
            UserResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/user",
     *     tags={"User"},
     *     summary="Create a user",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")),
     *     @OA\Response(response=200, description="User created successfully", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent( ref="#/components/schemas/Unauthenticated" ) ),
     * )
     */
    public function store(CreateUserRequest $request)
    {
        $data = [
            'names' => $request->input('names'),
            'lastnames' => $request->input('lastnames'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'typeuser_id' => $request->input('typeuser_id'),
        ];

        $user = User::create($data);
        return response()->json(new UserResource($user));
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = [
            'names' => $request->input('names'),
            'lastnames' => $request->input('lastnames'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'typeuser_id' => $request->input('typeuser_id'),
        ];

        $user->update($data);
        $user = User::find($id);

        return response()->json(new UserResource($user));
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userLogged = auth()->user();
        if ($user->id === $userLogged->id) {
            return response()->json(["message" => "You can't delete yourself"], 403);
        }

        $user->delete();
        return response()->json(["message" => "User deleted successfully"]);
    }
}
