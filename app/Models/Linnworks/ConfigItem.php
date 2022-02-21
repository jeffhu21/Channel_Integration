<?php

namespace App\Models\Linnworks;

class ConfigItem
{
    public function GetConfigValueType($k)
    {
        switch ($k) {
            case 'tString':
                return 'STRING';
                break;
            case 'tInt':
                return 'INT';
                break;
            case 'tDouble':
                return 'DOUBLE';
                break;
            case 'tBool':
                return 'BOOLEAN';
                break;
            case 'tPassword':
                return 'PASSWORD';
                break;
            case 'tList':
                return 'LIST';
                break;
            
            
        }
    }
    
    /*
    public $ConfigValueType=[
        'tString' => 'STRING',
        'tInt' => 'INT',
        'tDouble' => 'DOUBLE',
        'tBool' => 'BOOLEAN',
        'tPassword' => 'PASSWORD',
        'tList' => 'LIST'
    ];
    */
    
    public string $ConfigItemId;
    public string $Description;
    public string $GroupName;
    public bool $MustBeSpecified;
    public string $Name;
    public bool $ReadOnly;
    public string $SelectedValue;
    public int $Sortorder;
    public String $ValueType;
    public String $RegExValidation;
    public String $RegExError;
    public $ListValues=array();
    //public $ValueType=['STRING','INT','DOUBLE','BOOLEAN','PASSWORD','LIST'];
    
    public function __construct()
    {
        $this->ValueType=$this->GetConfigValueType('tString');
        $this->ListValues=array();
    }
}
