<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'date',
        'number',
        'documentType',
        'detail',
        'totalIncome',
        'totalExpense',
        'currency',
        'typePayment',
        'comment',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Person::class, 'supplier_id');
    }

}
