<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentConcept extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'name',
        'type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'number' => 'like',
        'name' => 'like',
        'type' => 'like',
    ];

    const sorts = [
        'id',
        'number',
        'name',
        'type',
    ];
}
