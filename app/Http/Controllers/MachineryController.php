<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexMachineryRequest;
use App\Http\Requests\MachineryRequest;
use App\Http\Resources\MachineryResource;
use App\Models\Machinery;
use Illuminate\Http\Request;

class MachineryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/machinery",
     *     tags={"Machinery"},
     *     summary="Obtiene todas las maquinarias con paginación",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), description="Número de página"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Elementos por página"),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string"), description="Columna para ordenar"),
     *     @OA\Parameter(name="direction", in="query", @OA\Schema(type="string", enum={"asc","desc"}), description="Dirección de ordenamiento"),
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), description="Filtro por nombre"),
     *     @OA\Parameter(name="code", in="query", @OA\Schema(type="string"), description="Filtro por código"),
     *     @OA\Response(
     *         response=200,
     *         description="Colección de maquinarias",
     *         @OA\JsonContent(ref="#/components/schemas/MachineryCollection")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(IndexMachineryRequest $request)
    {
        $query = Machinery::query();
        $query = Machinery::with('unit');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('code')) {
            $query->where('code', 'like', '%'.$request->code.'%');
        }
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perPage = $request->get('per_page', 10);
        $results = $query->paginate($perPage);

        return MachineryResource::collection($results);
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/machinery",
     *     tags={"Machinery"},
     *     summary="Crea una nueva maquinaria",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MachineryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria creada",
     *         @OA\JsonContent(ref="#/components/schemas/MachineryResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="El campo name es obligatorio.")
     *         )
     *     )
     * )
     */
    public function store(MachineryRequest $request)
    {
        $data = $request->validated();
        $machinery = Machinery::create($data);

        return new MachineryResource($machinery);
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/machinery/{id}",
     *     tags={"Machinery"},
     *     summary="Muestra la maquinaria especificada",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la maquinaria",
     *         @OA\JsonContent(ref="#/components/schemas/MachineryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Maquinaria no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Machinery not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $machinery = Machinery::with('unit')->find($id);

        if (!$machinery) {
            return response()->json(['message' => 'Machinery not found'], 404);
        }

        return new MachineryResource($machinery);
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/machinery/{id}",
     *     tags={"Machinery"},
     *     summary="Actualiza la maquinaria especificada",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MachineryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/MachineryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Maquinaria no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Machinery not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="El campo name es obligatorio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function update(MachineryRequest $request, $id)
    {
        $machinery = Machinery::find($id);
        if (!$machinery) {
            return response()->json(['message' => 'Machinery not found'], 404);
        }

        $data = $request->validated();
        $machinery->update($data);

        return new MachineryResource($machinery);
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/machinery/{id}",
     *     tags={"Machinery"},
     *     summary="Elimina la maquinaria especificada",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria eliminada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Machinery deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Maquinaria no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Machinery not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $machinery = Machinery::find($id);
        if (!$machinery) {
            return response()->json(['message' => 'Machinery not found'], 404);
        }

        $machinery->delete();
        return response()->json(['message' => 'Machinery deleted']);
    }
}
