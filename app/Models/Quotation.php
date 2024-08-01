<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    /**
     * $table->id();
     * $table->string('number');
     * $table->string('currencyType');
     * $table->string('price');
     * $table->string('detail');
     * $table->string('initialPrice');
     * $table->string('paymentRemainder');
     * $table->string('exchangeRate')->nullable();
     * $table->dateTime('date');
     *
     * $table->foreignId('currency_id')->nullable()->constrained();
     * $table->foreignId('client_id')->constrained();
     * $table->timestamps();
     */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'currencyType',
        'price',
        'detail',
        'initialPrice',
        'paymentRemainder',
        'exchangeRate',
        'date',
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
        'description' => 'like',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function client()
    {
        return $this->belongsTo(Person::class);
    }


}
