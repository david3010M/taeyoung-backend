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
        'date',
        'buyRate',
        'saleRate',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'datetime',
        'buyRate' => 'decimal:2',
        'saleRate' => 'decimal:2',
    ];

    const filters = [
        'date' => '<=',
        'buyRate' => 'like',
        'saleRate' => 'like',
    ];

    const sorts = [
        'id',
        'buyRate',
        'saleRate',
        'rate',
        'date',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function accountPayable()
    {
        return $this->hasMany(AccountPayable::class);
    }

    public function accountReceivable()
    {
        return $this->hasMany(AccountReceivable::class);
    }

}
