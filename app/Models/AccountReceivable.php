<?php

namespace App\Models;

use App\Models\Scopes\UpdateStatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivable extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'days',
        'date',
        'amount',
        'balance',
        'status',
        'client_id',
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
        'client_id' => '=',
        'client.filterName' => 'like',
        'client.country_id' => '=',
        'order_id' => '=',
        'currency$date' => 'like',
    ];

    const sorts = [
        'id',
        'date',
        'amount',
        'balance',
        'status',
        'client_id',
        'order_id',
        'currency_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UpdateStatusScope);
    }

    public function client()
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

    public function extensions()
    {
        return $this->hasMany(Extension::class, 'accountReceivable_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'accountReceivable_id');
    }

}
