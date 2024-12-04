<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SaleResource;
use App\Models\Order;
use App\Models\SparePart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/repuestos",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     summary="Get all spare parts",
     *     @OA\Parameter( name="code", in="query", description="Code of spare part", required=false, @OA\Schema( type="string" ) ),
     *     @OA\Parameter( name="name", in="query", description="Name of spare part", required=false, @OA\Schema( type="string" ) ),
     *     @OA\Response( response=200, description="Spare parts found" ),
     *     @OA\Response( response=400, description="Bad request" ),
     *     @OA\Response( response=401, description="Unauthorized" ),
     *     @OA\Response( response=404, description="Not found" ),
     *     @OA\Response( response=500, description="Internal server error" )
     * )
     */
    public function getRepuestos(Request $request)
    {
        $validator = validator($request->query(), [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $code = $request->query('code');
        $name = $request->query('name');

        $object = SparePart::where('code', 'like', '%' . $code . '%')
            ->where('name', 'like', '%' . $name . '%')->get();

        if ($object->isEmpty()) return response()->json(['message' => 'Not found'], 404);

        $pdf = Pdf::loadView('repuesto', [
//        return view('repuesto', [
            'repuestos' => $object,
            'code' => $code,
            'name' => $name,
        ]);

//        return $pdf->stream('repuestos.pdf');
        return $pdf->download('repuestos_' . date('Y-m-d') . '.pdf');
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/venta/{id}",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     summary="Get a sale",
     *     @OA\Parameter( name="id", in="path", description="ID of sale", required=true, @OA\Schema( type="integer" ) ),
     *     @OA\Response(
     *          response=200,
     *          description="Reporte de Venta en formato PDF",
     *          @OA\MediaType( mediaType="application/pdf", @OA\Schema(type="string", format="binary"))
     *      ),
     *     @OA\Response( response=400, description="Bad request" ),
     *     @OA\Response( response=401, description="Unauthorized" ),
     *     @OA\Response( response=404, description="Not found" ),
     *     @OA\Response( response=500, description="Internal server error" )
     * )
     */
    public function getSale(int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) return response()->json(['message' => 'Not found'], 404);

        $pdf = Pdf::loadView('sale', [
//        return view('sale', [
            'sale' => json_decode((SaleResource::make($sale)->forReport())->toJson()),
        ]);

//        return $pdf->stream('sales.pdf');
        return $pdf->download('venta_' . $sale->number . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/compra/{id}",
     *     tags={"Reports"},
     *     security={{"bearerAuth": {}}},
     *     summary="Get a purchase",
     *     @OA\Parameter( name="id", in="path", description="ID of sale", required=true, @OA\Schema( type="integer" ) ),
     *     @OA\Response(
     *          response=200,
     *          description="Reporte de Compra en formato PDF",
     *          @OA\MediaType( mediaType="application/pdf", @OA\Schema(type="string", format="binary"))
     *      ),
     *     @OA\Response( response=400, description="Bad request" ),
     *     @OA\Response( response=401, description="Unauthorized" ),
     *     @OA\Response( response=404, description="Not found" ),
     *     @OA\Response( response=500, description="Internal server error" )
     * )
     */
    public function getPurchase(int $id)
    {
        $purchase = Order::where('type', 'purchase')->find($id);
        if (!$purchase) return response()->json(['message' => 'Not found'], 404);

        $pdf = Pdf::loadView('purchase', [
//        return view('purchase', [
            'purchase' => json_decode(PurchaseResource::make($purchase)->toJson()),
        ]);

//        return $pdf->stream('purchase.pdf');
        return $pdf->download('compra_' . $purchase->number . '_' . date('Y-m-d') . '.pdf');
    }


}
