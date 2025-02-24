<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineryInventory extends Model
{
    use SoftDeletes;

    protected $table = 'machineries_inventory';

    protected $fillable = [
        'machinery_id',
        'real_stock',
        'contable_stock',
    ];

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'machinery_id');
    }
}
