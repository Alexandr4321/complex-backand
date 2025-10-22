<?php

namespace App\Auth\Models;

use App\System\Models\Model;

class PasswordReset extends Model
{
    
    protected $primaryKey = 'email';
    
    protected $fillable = [
        'email', 'token'
    ];
}
