<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\Extension;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    /**
     * SHOW ALL COMMITMENTS
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/extension",
     *     tags={"Extension"},
     *     summary="Show all extensions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="accountReceivable_id", in="query", description="ID of the accountReceivable", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Show all extensions", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     * )
     *
     */
    public function index(Request $request)
    {
        $accountReceivableId = $request->input('accountReceivable_id') ?? '';

        $extensions = Extension::when($accountReceivableId !== '', function ($query) use ($accountReceivableId) {
            return $query->where('accountReceivable_id', $accountReceivableId);
        })->get();

        return response()->json($extensions);
    }


    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/extension",
     *     tags={"Extension"},
     *     summary="Create an extension",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/ExtensionRequest")),
     *     @OA\Response(response="200", description="Create an extension", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="422", description="Error: Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'newEndDate' => 'required|date|after:accountReceivable.date',
            'reason' => 'required|string',
            'accountReceivable_id' => 'required|integer|exists:account_receivables,id',
        ]);
        if ($validator->fails()) return response()->json(['error' => $validator->errors()->first()], 422);

        $accountReceivable = AccountReceivable::find($request->accountReceivable_id);
        $accountReceivableDate = Carbon::parse($accountReceivable->date)->format('Y-m-d');
        $isAfter = Carbon::parse($request->newEndDate)->isAfter($accountReceivableDate);
        if (!$isAfter) return response()->json(['error' => 'La nueva fecha de pago ' . $request->newEndDate . ' debe ser posterior a la fecha de pago actual ' . $accountReceivableDate], 422);

        $data = [
            'oldEndDate' => $accountReceivableDate,
            'newEndDate' => $request->newEndDate,
            'reason' => $request->reason,
            'accountReceivable_id' => $accountReceivable->id,
        ];

        $extension = Extension::create($data);
        $accountReceivable->update(['date' => $extension->newEndDate]);
        $extension = Extension::find($extension->id);
        return response()->json($extension);
    }

    public function show(int $id)
    {
        $extension = Extension::find($id);
        if (!$extension) return response()->json(['error' => 'Extension not found'], 404);
        return response()->json($extension);
    }

    public function update(Request $request, int $id)
    {

    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/extension/{id}",
     *     tags={"Extension"},
     *     summary="Delete an extension",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter( name="id", in="path", required=true, description="ID of the extension", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Delete an extension", @OA\JsonContent(ref="#/components/schemas/Extension")),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(@OA\Property(property="error", type="string", example="Extension not found"))),
     *     @OA\Response(response="401", description="Error: Unauthorized", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $extension = Extension::find($id);
        $accountReceivable = AccountReceivable::find($extension->accountReceivable_id);
        $extension->delete();
        $accountReceivable->update(['date' => $extension->oldEndDate]);
        return response()->json(['message' => 'Extension deleted']);
    }
}
