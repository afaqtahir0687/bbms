<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'verification';
    protected $primaryKey = 'verification_id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function picture()
    {
        return $this->belongsTo(Picture::class, 'picture_id');
    }

    public function verifier()
    {
        return $this->belongsTo(AppUser::class, 'verified_by');
    }
}
