<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscogsApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        //'UserId',
        'consumer_key',
        'consumer_secret',
        'oauth_token',
        'oauth_secret',
        'oauth_verifier',
        'user_agent',
        'callback_url'
    ];
}
