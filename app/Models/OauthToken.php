<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OauthToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_key',
        'consumer_secret',
        'oauth_token',
        'oauth_secret',
    ];

    
}
