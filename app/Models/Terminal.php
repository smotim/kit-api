<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    use HasFactory;

    protected $table = 'terminals';

    protected $fillable = [
        'id',
        'geography_city_id',
        'lat',
        'lon',
        'address_code',
        'city_name',
        'phone',
        'email',
        'value',
    ];
}
