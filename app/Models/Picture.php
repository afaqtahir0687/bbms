<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $table = 'picture';
    protected $primaryKey = 'picture_id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

    public function uploader()
    {
        return $this->belongsTo(AppUser::class, 'uploaded_by');
    }

    public function verification()
    {
        return $this->hasOne(Verification::class, 'picture_id');
    }
}
