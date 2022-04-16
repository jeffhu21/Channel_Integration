<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class OauthToken extends Model
{
    use HasFactory;

    protected $fillable = [
        //'UserId',

        'app_user_id',
        //'app_owner_id',
        'oauth_token',
        'oauth_secret',
        'oauth_verifier'
    ];

    
    public function appUser()
    {
        return $this->belongsTo(AppUser::class);
    }

    /*
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}
