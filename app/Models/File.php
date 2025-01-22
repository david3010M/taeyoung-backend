<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'path',
        'type',
        'order_id',
        'quotation_id',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getPathAttribute($value): string
    {
        return asset($value);
    }
}
