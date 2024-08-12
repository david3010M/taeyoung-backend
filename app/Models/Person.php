<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'dni',
        'ruc',
        'names',
        'fatherSurname',
        'motherSurname',
        'businessName',
        'address',
        'email',
        'phone',
        'representativeDni',
        'representativeNames',
        'country_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const clientFilters = [
        'ruc',
        'businessName',
        'email',
        'phone',
        'representativeDni',
        'representativeNames',
        'country_id',
    ];

    const clientSorts = [
        'ruc',
        'businessName',
        'email',
        'phone',
        'representativeDni',
        'representativeNames',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


}
