<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    use HasFactory;

    protected $table = 'app_users';

    protected $fillable = [
        'UserId',
        'Email',
        'AccountName',
        'AuthorizationToken',
        'IsComplete',
        'StepName',
        'IsConfigActive',
        'ApiKey',
        'ApiSecretKey',
        'IsOauth',
        'IsPriceIncTax',
        'DownloadVirtualItems',
    ];

    
    public function OauthToken()
    {
        return $this->hasOne(OauthToken::class);
    }
    
    public function NotifyFailedDespatchedOrder()
    {
        return $this->hasMany(NotifyFailedDespatchedOrder::class);
    }
    
}
