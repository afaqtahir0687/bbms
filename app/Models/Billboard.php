<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billboard extends Model
{
    protected $table = 'billboard';
    protected $primaryKey = 'billboard_id';
    public $timestamps = false;
    protected $guarded = [];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function type()
    {
        return $this->belongsTo(BillboardType::class, 'billboard_type_id');
    }

    public function marketRating()
    {
         return $this->belongsTo(RatingScale::class, 'market_rating_id');
    }
}
