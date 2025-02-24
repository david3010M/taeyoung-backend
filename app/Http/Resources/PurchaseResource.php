<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="PurchaseResource",
 *   title="PurchaseResource",
 *   description="Recurso que representa una Compra",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="number", type="string", example="COMP-0001"),
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="detail", type="string", example="Detalle o comentario"),
 *   @OA\Property(property="documentType", type="string", enum={"BOLETA","FACTURA"}, example="FACTURA"),
 *   @OA\Property(property="paymentType", type="string", enum={"CONTADO","CREDITO"}, example="CONTADO"),
 *   @OA\Property(property="currencyType", type="string", enum={"USD","PEN"}, example="PEN"),
 *   @OA\Property(property="totalMachinery", type="number", format="float", example=1000),
 *   @OA\Property(property="totalSpareParts", type="number", format="float", example=500),
 *   @OA\Property(property="subtotal", type="number", format="float", example=1500),
 *   @OA\Property(property="total", type="number", format="float", example=1500),
 *   @OA\Property(property="totalExpense", type="number", format="float", example=1500),
 *   @OA\Property(property="balance", type="number", format="float", example=1500),
 *   @OA\Property(property="supplier_id", type="integer", example=1),
 *   @OA\Property(property="quotation_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="supplier", ref="#/components/schemas/Client"),
 *   @OA\Property(property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryResource")),
 *   @OA\Property(property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartResource"))
 * )
 *
 * @OA\Schema(
 *   schema="PurchaseCollection",
 *   title="PurchaseCollection",
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PurchaseResource")),
 *   @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *   @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'number'          => $this->number,
            'date'            => Carbon::parse($this->date)->format('Y-m-d'),
            'detail'          => $this->detail,

            'documentType'    => $this->documentType,
            'paymentType'     => $this->paymentType,
            'currencyType'    => $this->currencyType,
            'currencySymbol'  => $this->currencyType === 'USD' ? '$' : 'S/',

            'totalMachinery'  => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),
            'subtotal'        => round($this->subtotal, 2),
            'total'           => round($this->total, 2),
            'totalExpense'    => round($this->totalExpense, 2),
            'balance'         => round($this->balance, 2),

            'supplier_id'     => $this->supplier_id,
            'quotation_id'    => $this->quotation_id,
            'supplier'        => new ClientResource($this->supplier),
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts'=> DetailSparePartResource::collection($this->detailSpareParts),
            'files' => FileResource::collection($this->files),
        ];
    }
}
