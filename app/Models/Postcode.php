<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'postcode',
        'city',
        'state',
        'state_code',
    ];

    public function scopeByPostcode($query, $postcode)
    {
        return $query->where('postcode', $postcode);
    }

    public function scopeByState($query, $stateCode)
    {
        return $query->where('state_code', $stateCode);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }
}
