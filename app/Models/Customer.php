<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
    protected $guarded = [];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'customer_id');
    }
}
