<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AppUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'app_user';
    protected $primaryKey = 'user_id';
    public $timestamps = false; // ERD has no timestamps

    protected $fillable = [
        'login_email',
        'full_name',
        'status',
    ];

    // Since there's no password in ERD, we might need to override getAuthPassword to return null or something unique if we use standard Guard
    public function getAuthPassword()
    {
        return null; 
    }
}
