<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;
    protected $fillable = ['country_code', 'country_name', 'image_path', 'latitude', 'longitude', 'boundary_data'];

    public function provinces()
    {
        return $this->hasMany(Province::class, 'country_id');
    }
}
