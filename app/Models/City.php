<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class City extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cities';

    protected $fillable = [
        'city_id',
        'name',
        'region',
        'terminals_count',
        'updated_at'
    ];

    public function terminals()
    {
        return $this->hasMany(Terminal::class, 'city_id', 'city_id');
    }
}
