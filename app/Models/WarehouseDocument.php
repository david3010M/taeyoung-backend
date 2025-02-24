<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *   schema="WarehouseDocument",
 *   type="object",
 *   title="WarehouseDocument",
 *   required={"date","documentType","mode"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="number", type="string", example="DOC-001"),
 *   @OA\Property(property="documentType", type="string", enum={"ingreso","salida"}, example="ingreso"),
 *   @OA\Property(property="mode", type="string", enum={"Real","Contable"}, example="Real"),
 *   @OA\Property(property="reason", type="string", example="Ajuste de stock"),
 *   @OA\Property(property="comment", type="string", example="Observaciones varias")
 * )
 */
class WarehouseDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouse_documents';

    protected $fillable = [
        'date',
        'number',
        'documentType', // ingreso/salida
        'mode',         // Real/Contable
        'reason',
        'comment',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function details()
    {
        return $this->hasMany(WarehouseDocumentDetail::class, 'warehouse_document_id');
    }
}
