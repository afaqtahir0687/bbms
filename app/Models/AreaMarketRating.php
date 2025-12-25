<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaMarketRating extends Model
{
    protected $table = 'area_market_rating';
    protected $primaryKey = 'area_id'; // PK is area_id
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function ratingScale()
    {
        return $this->belongsTo(RatingScale::class, 'rating_id');
    }
}
