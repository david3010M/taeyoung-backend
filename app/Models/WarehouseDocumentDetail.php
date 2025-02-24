<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *   schema="WarehouseDocumentDetail",
 *   type="object",
 *   title="WarehouseDocumentDetail",
 *   required={"warehouse_document_id","quantity"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="warehouse_document_id", type="integer", example=10),
 *   @OA\Property(property="machinery_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="spare_part_id", type="integer", nullable=true, example=5),
 *   @OA\Property(property="quantity", type="integer", example=10)
 * )
 */
class WarehouseDocumentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouse_document_details';

    protected $fillable = [
        'warehouse_document_id',
        'machinery_id',
        'spare_part_id',
        'quantity',
    ];

    public function warehouseDocument()
    {
        return $this->belongsTo(WarehouseDocument::class, 'warehouse_document_id');
    }

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
