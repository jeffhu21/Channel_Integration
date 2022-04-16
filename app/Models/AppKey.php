<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppKey extends Model
{
    use HasFactory;

    protected $fillable = [
        //'user_id',
        'discogs_consumer_key',
        'discogs_consumer_secret',
        'linnworks_application_id',
        'linnworks_application_secret',
        
        'user_agent',
        'callback_url'
    ];

    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
