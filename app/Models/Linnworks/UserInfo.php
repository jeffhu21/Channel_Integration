<?php

namespace App\Models\Linnworks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_infos';

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
}
