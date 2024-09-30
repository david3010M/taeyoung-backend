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

        'isBankPayment',
        'deposit',
        'numberVoucher',
        'routeVoucher',

        'comment',

        'status',
//        'person_id',
        'user_id',
        'bank_id',
        'paymentConcept_id',
        'accountReceivable_id',
        'accountPayable_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
