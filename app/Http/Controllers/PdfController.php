<?php

namespace App\Http\Controllers;

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

//        $pdf = Pdf::loadView('repuesto', [
        return view('repuesto', [
            'repuestos' => $object,
            'code' => $code,
            'name' => $name,
        ]);

//        return $pdf->stream('repuestos.pdf');
//        return $pdf->download('repuestos_' . date('Y-m-d') . '.pdf');
    }

    public function getSales(Request $request, int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) return response()->json(['message' => 'Not found'], 404);

        $validator = validator($request->query(), [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $code = $request->query('code');
        $name = $request->query('name');

        $object = SparePart::where('code', 'like', '%' . $code . '%')
            ->where('name', 'like', '%' . $name . '%')->get();

//        $pdf = Pdf::loadView('sale', [
        return view('sale', [
            'code' => $code,
            'name' => $name,
            'sale' => json_decode((SaleResource::make($sale)->forReport())->toJson()),
        ]);

        return $pdf->stream('sales.pdf');
//        return $pdf->download('sales_' . date('Y-m-d') . '.pdf');
    }


}
