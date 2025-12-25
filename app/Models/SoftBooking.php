<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftBooking extends Model
{
    protected $table = 'soft_booking';
    protected $primaryKey = 'soft_booking_id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'hold_from' => 'datetime',
        'hold_to' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }
}
