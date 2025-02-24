<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePartInventory extends Model
{
    use SoftDeletes;

    protected $table = 'spare_parts_inventory';

    protected $fillable = [
        'spare_part_id',
        'real_stock',
        'contable_stock',
    ];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
