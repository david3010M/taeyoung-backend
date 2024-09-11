<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * - CUENTAS POR COBRAR Y CUENTAS POR PAGAR
 * - GASTOS DE VENTA(LO QUE VENDIO) COMPRA(LO QUE COMPRPO) Y OPERACIONES(LO QUE GASTO ADEMÁS DE COMPRA Y VENTA)
 * - COMPRA PAGADO, SALDO, ESTADO
 * - COMPRA: ESTADOS = PENDIENTE, PAGADO, ANULADO
 */
class DetailSparePart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'spare_part_id',
        'quotation_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'purchasePrice' => 'decimal:2',
        'salePrice' => 'decimal:2',
    ];

    const filters = [
        'quantity' => 'like',
        'movementType' => 'like',
        'purchasePrice' => 'like',
        'salePrice' => 'like',
        'purchaseValue' => 'like',
        'saleValue' => 'like',
        'order_id' => 'like',
        'spare_part_id' => 'like',
    ];

    const sorts = [
        'id',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'order_id',
        'spare_part_id',
    ];

    public static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            $model->sparePart->update([
                'purchasePrice' => $model->purchasePrice && $model->purchasePrice > 0 ? $model->purchasePrice : $model->sparePart->purchasePrice,
                'salePrice' => $model->salePrice && $model->salePrice > 0 ? $model->salePrice : $model->sparePart->salePrice,
            ]);
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

}
