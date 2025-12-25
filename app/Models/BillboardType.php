<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillboardType extends Model
{
    protected $table = 'billboard_type';
    protected $primaryKey = 'billboard_type_id';
    public $timestamps = false;
    protected $guarded = [];
}
