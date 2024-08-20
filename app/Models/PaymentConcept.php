<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="PaymentConcept",
 *     title="PaymentConcept",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="123456"),
 *     @OA\Property(property="name", type="string", example="name"),
 *     @OA\Property(property="type", type="string", example="type")
 * )
 */
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
