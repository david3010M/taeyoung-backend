<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'currencyFrom',
        'currencyTo',
        'rate',
        'date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'datetime',
        'rate' => 'decimal:2',
    ];

    const filters = [
        'currencyFrom' => 'like',
        'currencyTo' => 'like',
        'rate' => 'like',
        'date' => '<='
    ];

    const sorts = [
        'id',
        'currencyFrom',
        'currencyTo',
        'rate',
        'date',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

}
