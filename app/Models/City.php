<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'city_id';
    public $timestamps = false;
    protected $fillable = ['province_id', 'city_code', 'city_name', 'image_path', 'latitude', 'longitude', 'boundary_data'];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'city_id');
    }
}
