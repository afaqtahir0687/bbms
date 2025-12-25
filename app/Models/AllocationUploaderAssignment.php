<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllocationUploaderAssignment extends Model
{
    protected $table = 'allocation_uploader_assignment';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'assigned_on' => 'datetime',
        'active_flag' => 'boolean',
    ];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

    public function uploader()
    {
        return $this->belongsTo(AppUser::class, 'uploader_user_id');
    }
}
