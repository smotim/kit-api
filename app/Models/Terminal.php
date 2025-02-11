<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Terminal extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'terminals';

    protected $fillable = [
        'id',
        'geography_city_id',
        'lat',
        'lon',
        'address_code',
        'cityName',
        'phones',
        'emails',
        'value',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }
}
