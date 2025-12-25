<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'province';
    protected $primaryKey = 'province_id';
    public $timestamps = false;
    protected $fillable = ['country_id', 'province_code', 'province_name', 'image_path', 'latitude', 'longitude', 'boundary_data'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'province_id');
    }
}
