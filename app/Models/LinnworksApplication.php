<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinnworksApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        //'UserId',
        'application_id',
        'application_secret',
        'token'
    ];
}
