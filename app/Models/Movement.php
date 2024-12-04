<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'paymentDate',
        'typeDocument', // BOLETA O FACTURA

        'total',
        'cash',
        'yape',
        'plin',
        'card',

        'currencyType',
        'isBankPayment',
        'deposit',
//        'numberVoucher',
//        'routeVoucher',

        'comment',
        'status',
//        'person_id',
        'user_id',
        'bank_id',
//        'paymentConcept_id',
        'accountReceivable_id',
        'accountPayable_id',
        'currency_id',
//        'order_id',
    ];

    protected $casts = [
        'paymentDate' => 'date:Y-m-d',
        'total' => 'float',
        'cash' => 'float',
        'yape' => 'float',
        'plin' => 'float',
        'card' => 'float',
        'deposit' => 'float',
        'isBankPayment' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'paymentDate' => 'between',
        'total' => 'like',
        'cash' => 'like',
        'yape' => 'like',
        'plin' => 'like',
        'card' => 'like',
        'deposit' => 'like',
        'currencyType' => 'like',
        'typeDocument' => 'like',
        'isBankPayment' => 'like',
        'comment' => 'like',
        'status' => 'like',
        'user_id' => '=',
        'bank_id' => '=',
        'accountReceivable_id' => '=',
        'accountPayable_id' => '=',
        'currency_id' => '=',
    ];

    const sorts = [
        'id',
        'paymentDate',
        'total',
        'cash',
        'yape',
        'plin',
        'card',
        'deposit',
        'currencyType',
        'typeDocument',
        'isBankPayment',
        'comment',
        'status',
        'user_id',
        'bank_id',
        'accountReceivable_id',
        'accountPayable_id',
        'currency_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function accountReceivable()
    {
        return $this->belongsTo(AccountReceivable::class);
    }

    public function accountPayable()
    {
        return $this->belongsTo(AccountPayable::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
