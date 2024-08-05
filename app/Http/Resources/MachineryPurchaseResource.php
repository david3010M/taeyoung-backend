<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="MachineryPurchaseResource",
 *     title="MachineryPurchaseResource",
 *     description="Machinery Purchase resource",
 *     @OA\Property (property="id", type="integer", example="1" ),
 *     @OA\Property (property="number", type="string", example="123456" ),
 *     @OA\Property (property="date", type="string", example="2021-09-01" ),
 *     @OA\Property (property="supplier", type="string", example="Supplier Name" ),
 *     @OA\Property (property="country", type="string", example="Country Name" ),
 *     @OA\Property (property="features", type="string", example="Features" ),
 *     @OA\Property (property="quantity", type="integer", example="1" ),
 *     @OA\Property (property="total", type="number", example="1000.00" )
 * )
 *
 * @OA\Schema (
 *     schema="MachineryPurchaseResourceCollection",
 *     title="MachineryPurchaseResourceCollection",
 *     description="Machinery Purchase resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/MachineryPurchaseResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema (
 *     schema="MachineryPurchaseRequest",
 *     title="MachineryPurchaseRequest",
 *     description="Machinery Purchase request",
 *     required={"number", "date", "quantity", "features", "total", "supplier_id"},
 *     @OA\Property (property="number", type="string", example="COMAQ-0000099" ),
 *     @OA\Property (property="date", type="string", example="2021-09-01" ),
 *     @OA\Property (property="quantity", type="integer", example="1" ),
 *     @OA\Property (property="features", type="string", example="Features" ),
 *     @OA\Property (property="total", type="number", example="999.99" ),
 *     @OA\Property (property="supplier_id", type="integer", example="1" )
 * )
 *
 */
class MachineryPurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'documentType' => $this->documentType,
            'quantity' => $this->quantity,
            'detail' => $this->detail,
            'totalIncome' => $this->totalIncome,
            'totalExpense' => $this->totalExpense,
            'currency' => $this->currency,
            'typePayment' => $this->typePayment,
            'comment' => $this->comment,
            'supplier_id' => $this->supplier_id,
            'quotation_id' => $this->quotation_id,
            'supplier' => new SupplierResource($this->supplier),
//            'quotation' => new QuotationResource($this->quotation),
            'quotation' => $this->quotation,
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
        ];
    }
}
