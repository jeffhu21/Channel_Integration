<?php

namespace App\Models\Linnworks;

class ConfigItem
{
    
    public const ConfigValueType=[
        'tString' => 'STRING',
        'tInt' => 'INT',
        'tDouble' => 'DOUBLE',
        'tBool' => 'BOOLEAN',
        'tPassword' => 'PASSWORD',
        'tList' => 'LIST'
    ];
    
    public string $ConfigItemId;
    public string $Description;
    public string $GroupName;
    public boolean $MustBeSpecified;
    public string $Name;
    public boolean $ReadOnly;
    public string $SelectedValue;
    public integer $Sortorder;
    public String $ValueType;
    public String $RegExValidation;
    public String $RegExError;
    public $ListValues=array();
    //public $ValueType=['STRING','INT','DOUBLE','BOOLEAN','PASSWORD','LIST'];
    
    public function __construct()
    {
        $this->ValueType=ConfigValueType->tString;
        $this->ListValues=array();
    }
}
