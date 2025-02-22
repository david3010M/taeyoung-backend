<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @OA\Schema(
 *     schema="BankAccountModel",
 *     title="BankAccountModel",
 *     type="object",
 *     required={"name", "bank_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cuenta Corriente 01"),
 *     @OA\Property(property="bank_id", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="card_number", type="string", example="1234-5678-9012-3456"),
 *     @OA\Property(property="card_type", type="string", example="VISA"),
 * )
 */

class BankAccount extends Model
{
    use SoftDeletes;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'name',
        'bank_id',
        'status',
        'card_number',
        'card_type',
    ];

    /**
     * Relación con la tabla banks (cada BankAccount pertenece a un Bank).
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
