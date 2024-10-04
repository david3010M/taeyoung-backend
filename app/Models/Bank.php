<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Bank",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Bank BCA")
 * )
 *
 * @OA\Schema (
 *     schema="BankRequest",
 *     @OA\Property(property="name", type="string", example="Bank BCA")
 * )
 *
 */
class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'name',
    ];

    const sorts = [
        'id',
        'name',
    ];

    public function payments()
    {
//        return $this->hasMany(Payment::class);
    }

}
