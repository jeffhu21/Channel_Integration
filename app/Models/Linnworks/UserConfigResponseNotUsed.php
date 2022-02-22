<?php

namespace App\Models\Linnworks;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use App\Models\Linnworks\ConfigItem;

class UserConfigResponse
{
    //use HasFactory;
    
    public $StepName,$WizardStepDescription,$WizardStepTitle;
    public $Error;
    public $ConfigItems = [];
    

}
