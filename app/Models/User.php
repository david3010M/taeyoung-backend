<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property( property="id", type="integer", example="1" ),
 *     @OA\Property( property="names", type="string", example="John" ),
 *     @OA\Property( property="lastnames", type="string", example="Doe" ),
 *     @OA\Property( property="username", type="string", example="johndoe" ),
 *     @OA\Property( property="typeuser_id", type="integer", example="1" ),
 *     @OA\Property( property="typeuser", type="object", ref="#/components/schemas/TypeUser" ),
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'names',
        'lastnames',
        'username',
        'typeuser_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function typeuser()
    {
        return $this->belongsTo(TypeUser::class);
    }
}
