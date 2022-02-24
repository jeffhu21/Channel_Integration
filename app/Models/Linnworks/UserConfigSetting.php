<?php

namespace App\Models\Linnworks;

class UserConfigSetting
{
    public $UserConfig = [
        'Error'=>'',
        'StepName'=>'',
        'WizardStepDescription'=>'',
        'WizardStepTitle'=>'',
        'ConfigItems'=>[] //Array of ConfigItem
    ];

    public $ConfigItem=[
        'ConfigItemId'=>'',
        'Description'=>'',
        'GroupName'=>'',
        'MustBeSpecified'=>'',
        'Name'=>'',
        'ReadOnly'=>'',
        'SelectedValue'=>'',
        'Sortorder'=>'',
        'ValueType'=>'',
        'RegExValidation'=>'',
        'RegExError'=>'',
        'ListValues'=>[] //Array of ListItem
    ];

    public $ListItem =[
        'Display'=>'',
        'Value'=>''
    ];
}


