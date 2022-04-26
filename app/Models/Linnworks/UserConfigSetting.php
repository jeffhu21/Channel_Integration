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
        //'ValueType'=>config('linnworksHelper.ConfigValueType.String'),
        'RegExValidation'=>'',
        'RegExError'=>'',
        'ListValues'=>[] //Array of ListItem
    ];

    public $ConfigValueType=[
        'String'=>'STRING',
        'Int'=>'INT',
        'Double'=>'DOUBLE',
        'Boolean'=>'BOOLEAN',
        'Password'=>'PASSWORD',
        'List'=>'LIST',
        'Guid'=>'GUID'
    ];

    public $ListItem =[
        'Display'=>'',
        'Value'=>''
    ];
}


