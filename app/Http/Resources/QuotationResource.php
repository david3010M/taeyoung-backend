<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="QuotationResource",
 *     required={"id", "number", "date", "detail", "paymentType", "currencyType", "totalMachinery", "totalSpareParts", "subtotal", "igv", "discount", "total", "client_id"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="COTI-0001"),
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="paymentType", type="string", example="Contado"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="totalMachinery", type="number", example="100"),
 *     @OA\Property(property="totalSpareParts", type="number", example="100"),
 *     @OA\Property(property="subtotal", type="number", example="100"),
 *     @OA\Property(property="igv", type="number", example="18"),
 *     @OA\Property(property="discount", type="number", example="0"),
 *     @OA\Property(property="total", type="number", example="118"),
 *     @OA\Property(property="client_id", type="integer", example="1"),
 *     @OA\Property(property="client", type="string", example="Client Name"),
 *     @OA\Property(property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryResource")),
 *     @OA\Property(property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartResource"))
 * )
 *
 * @OA\Schema (
 *     schema="QuotationCollection",
 *     title="QuotationCollection",
 *     description="Quotation resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/QuotationResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => "COTI-" . $this->number,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'detail' => $this->detail,

            'paymentType' => $this->paymentType, // Contado o Crédito
            'currencyType' => $this->currencyType,
            'currencySymbol' => $this->currencyType === 'USD' ? '$' : 'S/',
            'totalMachinery' => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),
            'subtotal' => round($this->subtotal, 2),
            'igvActive' => $this->igvActive,
            'igv' => round($this->igv, 2),
            'discount' => round($this->discount, 2),
            'total' => round($this->total, 2),
            'client_id' => $this->client_id,
            'client' => ClientResource::make($this->client)->filterName,
            'clientData' => ClientResource::make($this->client),
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
            'files' => FileResource::collection($this->files),
            'images' => FileResource::collection($this->images),
        ];
    }
}
