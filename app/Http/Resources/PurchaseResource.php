<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="PurchaseResource",
 *     title="PurchaseResource",
 *     description="Machinery Purchase resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="number", type="string", example="COMPR-0001"),
 *     @OA\Property (property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property (property="detail", type="string", example="This is a detail"),
 *     @OA\Property (property="paymentType", type="string", example="Contado"),
 *     @OA\Property (property="currencyType", type="string", example="USD"),
 *     @OA\Property (property="totalMachinery", type="number", example="100"),
 *     @OA\Property (property="totalSpareParts", type="number", example="100"),
 *     @OA\Property (property="subtotal", type="number", example="100"),
 *     @OA\Property (property="igv", type="number", example="18"),
 *     @OA\Property (property="discount", type="number", example="0"),
 *     @OA\Property (property="total", type="number", example="118"),
 *     @OA\Property (property="totalIncome", type="number", example="0"),
 *     @OA\Property (property="totalExpense", type="number", example="0"),
 *     @OA\Property (property="comment", type="string", example="This is a comment"),
 *     @OA\Property (property="supplier_id", type="integer", example="1"),
 *     @OA\Property (property="quotation_id", type="integer", example="21"),
 *     @OA\Property (property="supplier", type="object", ref="#/components/schemas/SupplierResource"),
 *     @OA\Property (property="quotation", type="object", ref="#/components/schemas/Quotation"),
 *     @OA\Property (property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryResource")),
 *     @OA\Property (property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartResource"))
 * )
 *
 * @OA\Schema (
 *     schema="PurchaseCollection",
 *     title="PurchaseCollection",
 *     description="Purchase resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/PurchaseResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => "COMPR-" . $this->number,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'detail' => $this->detail,

            'paymentType' => $this->paymentType,
            'currencyType' => $this->currencyType,
            'totalMachinery' => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),

            'subtotal' => round($this->subtotal, 2),
            'igv' => round($this->igv, 2),
            'discount' => round($this->discount, 2),
            'total' => round($this->total, 2),

            'totalIncome' => round($this->totalIncome, 2),
            'totalExpense' => round($this->totalExpense, 2),

            'comment' => $this->comment,

            'supplier_id' => $this->supplier_id,
            'quotation_id' => $this->quotation_id,

            'supplier' => new SupplierResource($this->supplier),
            'quotation' => $this->quotation,
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
        ];
    }
}
