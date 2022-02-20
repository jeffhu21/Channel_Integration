<?php

namespace App\Models\Linnworks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model
{
    use HasFactory;

    protected $table = 'user_configs';

    protected $fillable = [
        'user_id',
        'email',
        'account_name',
        'authorization_token',
        'is_complete',
        'step_name',
        'is_config_active',
        'api_key',
        'api_secret_key',
        'is_oauth',
        'is_price_inc_tax',
        'download_virtual_items',
    ];
}
