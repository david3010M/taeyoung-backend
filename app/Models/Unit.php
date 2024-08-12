<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const filters = [
        'name',
        'abbreviation'
    ];

    const sorts = [
        'id',
        'name',
        'abbreviation'
    ];

    public function spareParts()
    {
        return $this->hasMany(SparePart::class);
    }
}
