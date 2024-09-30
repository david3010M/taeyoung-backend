<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema (
 *     schema="SaleResource",
 *     title="SaleResource",
 *     description="Machinery Sale resource",
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
 *     @OA\Property (property="client_id", type="integer", example="1"),
 *     @OA\Property (property="quotation_id", type="integer", example="21"),
 *     @OA\Property (property="client", type="object", ref="#/components/schemas/Client"),
 *     @OA\Property (property="quotation", type="object", ref="#/components/schemas/Quotation"),
 *     @OA\Property (property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryResource")),
 *     @OA\Property (property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartResource"))
 * )
 *
 * @OA\Schema (
 *     schema="SaleCollection",
 *     title="SaleCollection",
 *     description="Sale resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/SaleResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'documentType' => $this->documentType,
            'paymentType' => $this->paymentType,
            'number' => "VENT-" . $this->number,
            'client' => (new ClientResource($this->client))->filterName,
            'total' => round($this->total, 2),
            'balance' => round($this->balance, 2),
            'status' => $this->status,


            'detail' => $this->detail,

            'currencyType' => $this->currencyType,
            'totalMachinery' => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),

            'subtotal' => round($this->subtotal, 2),
            'igv' => round($this->igv, 2),
            'discount' => round($this->discount, 2),

            'totalIncome' => round($this->totalIncome, 2),
            'totalExpense' => round($this->totalExpense, 2),

            'comment' => $this->comment,

            'client_id' => $this->client_id,
            'quotation_id' => $this->quotation_id,

            'accountReceivable' => AccountReceivableResource::collection($this->accountReceivable),
            'quotation' => new SingleQuotationResource($this->quotation),
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
        ];
    }
}
