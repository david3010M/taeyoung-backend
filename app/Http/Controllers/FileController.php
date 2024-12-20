<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/file",
     *     tags={"File"},
     *     summary="Get all files",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Files", @OA\JsonContent(ref="#/components/schemas/FileCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function index()
    {
        $files = File::paginate(10);
        return FileResource::collection($files);
    }


    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/quotation/{id}/file",
     *     tags={"File"},
     *     summary="Store a new file for a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Quotation id", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/FileRequest"))),
     *     @OA\Response(response=200, description="File created", @OA\JsonContent(ref="#/components/schemas/FileCollection")),
     *     @OA\Response(response=404, description="Quotation not found", @OA\JsonContent( type="object", @OA\Property(property="message", type="string", example="Quotation not found"))),
     * )
     */
    public function storeQuotationFile(FileRequest $request, int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);

        $files = $request->file('files');
        foreach ($files as $file) {
            $currentTime = now();
            $originalName = str_replace(' ', '_', $file->getClientOriginalName());
            $filename = $currentTime->format('YmdHis') . '_' . $originalName;
            $file->storeAs('public/quotation', $filename);
            $routeImage = asset('storage/quotation/' . $filename);
            $dataImage = [
                'path' => $routeImage,
                'quotation_id' => $quotation->id,
            ];
            File::create($dataImage);
        }

        $filesAdded = File::where('quotation_id', $quotation->id)->get();

        return response()->json($filesAdded);
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/order/{id}/file",
     *     tags={"File"},
     *     summary="Store a new file for a order",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Order id", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/FileRequest"))),
     *     @OA\Response(response=200, description="File created", @OA\JsonContent(ref="#/components/schemas/FileCollection")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent( type="object", @OA\Property(property="message", type="string", example="Order not found"))),
     * )
     */
    public function storeOrderFile(FileRequest $request, int $id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $files = $request->file('files');
        foreach ($files as $file) {
            $currentTime = now();
            $originalName = str_replace(' ', '_', $file->getClientOriginalName());
            $filename = $currentTime->format('YmdHis') . '_' . $originalName;
            $file->storeAs('public/order', $filename);
            $routeImage = asset('storage/order/' . $filename);
            $dataImage = [
                'path' => $routeImage,
                'order_id' => $order->id,
            ];
            File::create($dataImage);
        }

        $filesAdded = File::where('order_id', $order->id)->get();

        return response()->json($filesAdded);
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/file/{id}",
     *     tags={"File"},
     *     summary="Delete a file",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="File id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="File deleted", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="File deleted"))),
     *     @OA\Response(response=404, description="File not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="File not found")))
     * )
     */
    public function destroy(int $id)
    {
        $file = File::find($id);
        if (!$file) return response()->json(['message' => 'File not found'], 404);

        if ($file) {
            $relativePath = str_replace(asset('storage') . '/', '', $file->path);
            if (Storage::exists('public/' . $relativePath)) {
                Storage::delete('public/' . $relativePath);
            }
            $file->delete();
        }
        return response()->json(['message' => 'File deleted']);
    }
}

