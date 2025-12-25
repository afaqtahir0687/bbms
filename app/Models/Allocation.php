<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $table = 'allocation';
    protected $primaryKey = 'allocation_id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'allocated_from' => 'date',
        'allocated_to' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }
    
    public function assignments()
    {
        return $this->hasMany(AllocationUploaderAssignment::class, 'allocation_id');
    }

    public function pictures()
    {
        return $this->hasMany(Picture::class, 'allocation_id');
    }
}
