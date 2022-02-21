<?php

namespace App\Models\Linnworks;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use App\Models\Linnworks\ConfigItem;

class UserConfigResponse
{
    //use HasFactory;

    public String $StepName,$WizardStepDescription,$WizardStepTitle;
    public $ConfigItems = [];

}
