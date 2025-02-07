<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Terminal extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'terminals';

    protected $fillable = [
        'terminal_id',
        'city_id',
        'name',
        'address',
        'phone',
        'working_hours',
        'coordinates',
        'updated_at'
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }
}