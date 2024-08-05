<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'detail',
        'date',
        'currencyType',
        'price', // CALCULATED
        'initialPayment', // CALCULATED
        'balance', // CALCULATED
        'debts',
        'exchangeRate', // CALCULATED
        'currency_id',
        'client_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    const filters = [
        'date' => '<=',
        'number' => 'like',
        'description' => 'like',
    ];

    const sorts = [
        'id',
        'number',
        'date',
        'currencyFrom',
        'currencyTo',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function client()
    {
        return $this->belongsTo(Person::class);
    }

    public function detailSpareParts()
    {
        return $this->hasMany(DetailSparePart::class);
    }

    public function detailMachinery()
    {
        return $this->hasMany(DetailMachinery::class);
    }


}
