<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccountRequest;
use App\Http\Requests\IndexBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/bankaccount",
     *     tags={"BankAccount"},
     *     summary="Lista las cuentas bancarias con paginación",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), description="Número de página"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Elementos por página"),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string"), description="Columna para ordenar"),
     *     @OA\Parameter(name="direction", in="query", @OA\Schema(type="string", enum={"asc","desc"}), description="Dirección de orden"),
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), description="Filtro por nombre de cuenta"),
     *     @OA\Parameter(name="bank_id", in="query", @OA\Schema(type="integer"), description="Filtro por ID de banco"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtro por estado (active, etc.)"),
     *     @OA\Response(
     *         response=200,
     *         description="Colección de cuentas bancarias",
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountCollection")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function index(IndexBankAccountRequest $request)
    {
        $query = BankAccount::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perPage = $request->input('per_page', 10);
        $results = $query->paginate($perPage);

        return BankAccountResource::collection($results);
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/bankaccount",
     *     tags={"BankAccount"},
     *     summary="Crea una nueva cuenta bancaria",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cuenta bancaria creada",
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="El campo name ya existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function store(BankAccountRequest $request)
    {
        $data = $request->validated();
        $bankAccount = BankAccount::create($data);

        return new BankAccountResource($bankAccount);
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/bankaccount/{id}",
     *     tags={"BankAccount"},
     *     summary="Muestra una cuenta bancaria específica",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la cuenta bancaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la cuenta bancaria",
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cuenta bancaria no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BankAccount not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function show($id)
    {
        $bankAccount = BankAccount::find($id);
        if (!$bankAccount) {
            return response()->json(['message' => 'BankAccount not found'], 404);
        }

        return new BankAccountResource($bankAccount);
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/bankaccount/{id}",
     *     tags={"BankAccount"},
     *     summary="Actualiza una cuenta bancaria específica",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la cuenta bancaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cuenta bancaria actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/BankAccountResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cuenta bancaria no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BankAccount not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="El campo name ya existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function update(BankAccountRequest $request, $id)
    {
        $bankAccount = BankAccount::find($id);
        if (!$bankAccount) {
            return response()->json(['message' => 'BankAccount not found'], 404);
        }

        $data = $request->validated();
        $bankAccount->update($data);

        return new BankAccountResource($bankAccount);
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/bankaccount/{id}",
     *     tags={"BankAccount"},
     *     summary="Elimina una cuenta bancaria específica",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la cuenta bancaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cuenta bancaria eliminada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="BankAccount deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cuenta bancaria no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BankAccount not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function destroy($id)
    {
        $bankAccount = BankAccount::find($id);
        if (!$bankAccount) {
            return response()->json(['message' => 'BankAccount not found'], 404);
        }

        $bankAccount->delete();
        return response()->json(['message' => 'BankAccount deleted']);
    }
}
