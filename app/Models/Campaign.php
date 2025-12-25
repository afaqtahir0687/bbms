<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaign';
    protected $primaryKey = 'campaign_id';
    public $timestamps = false;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function softBookings()
    {
        return $this->hasMany(SoftBooking::class, 'campaign_id');
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'campaign_id');
    }
}
