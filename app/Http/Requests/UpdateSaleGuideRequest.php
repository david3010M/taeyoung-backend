<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateSaleGuideRequest",
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="number", type="string", example="GUIDE-001"),
 *   @OA\Property(property="documentType", type="string", example="GUIA"),
 *   @OA\Property(property="client_id", type="integer", example=10),
 *   @OA\Property(property="detail", type="string", example="Guía actualizada"),
 *   @OA\Property(
 *     property="detailMachinery",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="machinery_id", type="integer", example=1),
 *       @OA\Property(property="quantity", type="integer", example=5),
 *       @OA\Property(property="contablePrice", type="number", example=100),
 *       @OA\Property(property="realPrice", type="number", example=0)
 *     )
 *   ),
 *   @OA\Property(
 *     property="detailSpareParts",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="spare_part_id", type="integer", example=2),
 *       @OA\Property(property="quantity", type="integer", example=3),
 *       @OA\Property(property="contablePrice", type="number", example=200),
 *       @OA\Property(property="realPrice", type="number", example=0)
 *     )
 *   )
 * )
 */
class UpdateSaleGuideRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'date'         => 'nullable|date',
            'number'       => 'nullable|string',
            'documentType' => 'nullable|string',
            'client_id'    => [
                'nullable',
                Rule::exists('people','id')
                    ->where('type','client')
                    ->whereNull('deleted_at')
            ],
            'detail'       => 'nullable|string',

            'detailMachinery'                   => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.machinery_id'    => 'required|integer|exists:machineries,id',
            'detailMachinery.*.quantity'        => 'required|integer|min:1',
            'detailMachinery.*.contablePrice'   => 'nullable|numeric',
            'detailMachinery.*.realPrice'       => 'nullable|numeric',

            'detailSpareParts'                  => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.spare_part_id'  => 'required|integer|exists:spare_parts,id',
            'detailSpareParts.*.quantity'       => 'required|integer|min:1',
            'detailSpareParts.*.contablePrice'  => 'nullable|numeric',
            'detailSpareParts.*.realPrice'      => 'nullable|numeric',
        ];
    }
}
