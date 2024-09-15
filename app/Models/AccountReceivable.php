<?php

namespace App\Models;

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
        'amount' => 'decimal:2',
    ];

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


}
