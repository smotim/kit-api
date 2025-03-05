<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Terminal extends Model
{
    use HasFactory;

    protected $collection = 'terminals';

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
//    public function city()
//    {
//        return $this->belongsTo(City::class, 'city_id', 'city_id');
//    }
}
