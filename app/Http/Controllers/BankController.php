<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexBankRequest;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankController extends Controller
{
    /**
     * Get all banks with pagination
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/bank",
     *     tags={"Bank"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="page", in="query", description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="per_page", in="query", description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="sort", in="query", description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter( name="direction", in="query", description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter( name="name", in="query", description="Filter by name", @OA\Schema(type="string")),
     *     @OA\Response( response=200, description="Bank collection response", @OA\JsonContent(ref="#/components/schemas/BankCollection")),
     *     @OA\Response( response=401, description="Unauthorized", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated")))
     * )
     */
    public function index(IndexBankRequest $request)
    {
        return $this->getFilteredResults(
            Bank::class,
            $request,
            Bank::filters,
            Bank::sorts,
            BankResource::class
        );
    }

    /**
     * Store a newly created bank in storage.
     * @OA\Post (
     *      path="/taeyoung-backend/public/api/bank",
     *      tags={"Bank"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BankRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Bank created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Bank")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('banks', 'name')->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $bank = Bank::create($data);
        $bank = Bank::find($bank->id);

        return response()->json($bank);
    }

    /**
     * Display the specified bank.
     * @OA\Get (
     *      path="/taeyoung-backend/public/api/bank/{id}",
     *      tags={"Bank"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Bank ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Bank found",
     *          @OA\JsonContent(ref="#/components/schemas/Bank")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Bank not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Bank not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */
    public function show(int $id)
    {
        $bank = Bank::find($id);

        if ($bank === null) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        return response()->json($bank);
    }

    /**
     * Update the specified bank in storage.
     * @OA\Put (
     *      path="/taeyoung-backend/public/api/bank/{id}",
     *      tags={"Bank"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Bank ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BankRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Bank updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Bank")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Bank not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Bank not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The name has already been taken.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */
    public function update(Request $request, int $id)
    {
        $bank = Bank::find($id);

        if ($bank === null) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('banks', 'name')->whereNull('deleted_at')->ignore($bank->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $bank->update($data);
        $bank = Bank::find($bank->id);

        return response()->json($bank);
    }

    /**
     * Remove the specified bank from storage.
     * @OA\Delete (
     *      path="/taeyoung-backend/public/api/bank/{id}",
     *      tags={"Bank"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Bank ID",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Bank deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Bank deleted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Bank not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Bank not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */
    public function destroy(int $id)
    {
        $bank = Bank::find($id);

        if ($bank === null) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        $bank->delete();

        return response()->json(['message' => 'Bank deleted']);
    }
}
