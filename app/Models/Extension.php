<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Extension",
 *     title="Extension",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="oldEndDate", type="string", example="2024-06-27"),
 *     @OA\Property(property="newEndDate", type="string", example="2024-06-27"),
 *     @OA\Property(property="reason", type="string", example="Razón de la extensión"),
 *     @OA\Property(property="accountReceivable_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", example="2024-06-27 22:59:36")
 * )
 *
 * @OA\Schema (
 *     schema="ExtensionRequest",
 *     title="ExtensionRequest",
 *     required={"newEndDate", "reason", "accountReceivable_id"},
 *     @OA\Property(property="newEndDate", type="string", example="2024-06-27"),
 *     @OA\Property(property="reason", type="string", example="Razón de la extensión"),
 *     @OA\Property(property="accountReceivable_id", type="integer", example="1")
 * )
 */
class Extension extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'oldEndDate',
        'newEndDate',
        'reason',
        'accountReceivable_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'oldEndDate' => 'date:d/m/Y',
        'newEndDate' => 'date:d/m/Y',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function accountReceivable()
    {
        return $this->belongsTo(AccountReceivable::class);
    }
}
