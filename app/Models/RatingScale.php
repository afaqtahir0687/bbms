<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingScale extends Model
{
    protected $table = 'rating_scale';
    protected $primaryKey = 'rating_id';
    public $timestamps = false;
    protected $guarded = [];
}
