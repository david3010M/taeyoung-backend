<?php

namespace App\Models;

use App\Models\Scopes\UpdateStatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'days',
        'date',
        'amount',
        'balance',
        'status',
        'supplier_id',
        'currency_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'amount' => 'float',
        'balance' => 'float',
    ];

    const filters = [
        'date' => 'between',
        'amount' => 'like',
        'balance' => 'like',
        'status' => 'like',
        'supplier_id' => '=',
        'order_id' => '=',
        'currency$date' => 'like',
    ];

    const sorts = [
        'id',
        'date',
        'amount',
        'balance',
        'status',
        'supplier_id',
        'order_id',
        'currency_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UpdateStatusScope);
    }

    public function supplier()
    {
        return $this->belongsTo(Person::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'accountPayable_id');
    }
}
