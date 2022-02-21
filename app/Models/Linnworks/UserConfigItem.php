<?php

//Not used. Delete later.

namespace App\Models\Linnworks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Linnworks\ListItem;

class UserConfigItem extends Model
{
    use HasFactory;

    public $ListValues = [];
    public String $RegExValidation,$RegExError;

    public function user_config()
    {
        return $this->belongsToMany(Linnworks\UserConfig::class);
    }
}
