<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="StoreWarehouseDocumentRequest",
 *   type="object",
 *   required={"date","documentType","mode","details"},
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="number", type="string", example="DOC-001", description="Opcional. Si no se envía, se autogenera."),
 *   @OA\Property(property="documentType", type="string", enum={"ingreso","salida"}, example="ingreso"),
 *   @OA\Property(property="mode", type="string", enum={"Real","Contable"}, example="Real"),
 *   @OA\Property(property="reason", type="string", example="Ajuste de stock"),
 *   @OA\Property(property="comment", type="string", example="Observaciones varias"),
 *   @OA\Property(
 *     property="details",
 *     type="array",
 *     description="Detalle de items (maquinaria o repuesto). Solo uno de machinery_id o spare_part_id en cada línea",
 *     @OA\Items(
 *       @OA\Property(property="machinery_id", type="integer", nullable=true, example=1),
 *       @OA\Property(property="spare_part_id", type="integer", nullable=true, example=null),
 *       @OA\Property(property="quantity", type="integer", example=10)
 *     )
 *   )
 * )
 */
class StoreWarehouseDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'         => 'required|date',
            'number'       => 'nullable|string', // Si no se envía, se autogenera en el controller
            'documentType' => ['required', Rule::in(['ingreso','salida'])],
            'mode'         => ['required', Rule::in(['Real','Contable'])],
            'reason'       => 'nullable|string',
            'comment'      => 'nullable|string',

            'details'                  => 'required|array|min:1',
            'details.*.machinery_id'   => 'nullable|integer|exists:machineries,id',
            'details.*.spare_part_id'  => 'nullable|integer|exists:spare_parts,id',
            'details.*.quantity'       => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($val) {
            $details = $this->input('details', []);
            foreach ($details as $idx => $detail) {
                $machinery = $detail['machinery_id'] ?? null;
                $spare     = $detail['spare_part_id'] ?? null;

                if ($machinery && $spare) {
                    $val->errors()->add(
                        "details.$idx",
                        "No puede especificar machinery_id y spare_part_id a la vez."
                    );
                }
                if (!$machinery && !$spare) {
                    $val->errors()->add(
                        "details.$idx",
                        "Debe especificar machinery_id o spare_part_id (uno de los dos)."
                    );
                }
            }
        });
    }
}
