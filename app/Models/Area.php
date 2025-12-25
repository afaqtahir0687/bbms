<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'area_id';
    public $timestamps = false;
    protected $fillable = ['city_id', 'area_code', 'area_name', 'image_path', 'latitude', 'longitude', 'boundary_data'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function marketRating()
    {
        return $this->hasOne(AreaMarketRating::class, 'area_id');
    }
    
    public function billboards()
    {
        return $this->hasMany(Billboard::class, 'area_id');
    }
}
