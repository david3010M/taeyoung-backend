<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSparePartRequest;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\SparePartResource;
use App\Models\Quotation;
use App\Models\SparePart;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SparePartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/spareparts",
     *     summary="List all spare parts",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="pagination",
     *         in="query",
     *         description="Number of items to display per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             minimum=5
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of spare parts",
     *         @OA\JsonContent(ref="#/components/schemas/SparePartPagination")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function index(IndexSparePartRequest $request)
    {
        return $this->getFilteredResults(
            SparePart::class,
            $request,
            SparePart::filters,
            SparePartResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/spareparts",
     *     summary="Create a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SparePartRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Spare part created",
     *         @OA\JsonContent(ref="#/components/schemas/SparePartResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'code' => [
                'required',
                'string',
                Rule::unique('spare_parts', 'code')->whereNull('deleted_at'),
            ],
            'name' => 'required|string',
            'purchasePrice' => 'required|numeric',
            'salePrice' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'purchasePrice' => $request->input('purchasePrice'),
            'salePrice' => $request->input('salePrice'),
            'stock' => $request->input('stock'),
        ];

        $sparePart = SparePart::create($data);
        $sparePart = SparePart::find($sparePart->id);

        return response()->json($sparePart);
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/spareparts/{id}",
     *     summary="Show a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Spare part ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             minimum=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Spare part detail",
     *         @OA\JsonContent(ref="#/components/schemas/SparePartResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Spare part not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Spare part not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function show(int $id)
    {
        $sparePart = SparePart::find($id);

        if ($sparePart === null) {
            return response()->json(['message' => 'Spare part not found'], 404);
        }

        return response()->json($sparePart);
    }

    public function update(Request $request, int $id)
    {
        $sparePart = SparePart::find($id);

        if ($sparePart === null) {
            return response()->json(['message' => 'Spare part not found'], 404);
        }

        $validator = validator($request->all(), [
            'code' => [
                'required',
                'string',
                Rule::unique('spare_parts', 'code')->whereNull('deleted_at')->ignore($sparePart->id),
            ],
            'name' => 'required|string',
            'purchasePrice' => 'required|numeric',
            'salePrice' => 'required|numeric',
            'stock' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'purchasePrice' => $request->input('purchasePrice'),
            'salePrice' => $request->input('salePrice'),
            'stock' => $request->input('stock'),
        ];

        $sparePart->update($data);
        $sparePart = SparePart::find($sparePart->id);

        return response()->json($sparePart);
    }

    public function destroy(int $id)
    {
        $sparePart = SparePart::find($id);

        if ($sparePart === null) {
            return response()->json(['message' => 'Spare part not found'], 404);
        }

        $sparePart->delete();

        return response()->json(['message' => 'Spare part deleted']);
    }
}
