<?php

namespace App\Models\Linnworks;

//use App\Models\Linnworks\ConfigItem as ConfigItem;
use App\Models\Linnworks\UserConfig as UserConfig;
//use App\Models\Linnworks\UserConfigResponse as UserConfigResponse;

class ConfigStage
{

    //Load UserConfig from UserConfig Model
    public static function loadUserConfig($token)
    {
        $UserConfig=UserConfig::where('AuthorizationToken',$token)->first();
        return $UserConfig;
    }

    public static function ConfigSetUp($UserConfig,$action)
    {
        $result=null;

        if($UserConfig->StepName == 'AddCredentials')
        {
            $result = self::getApiCredentials($UserConfig);
        }
        else if($UserConfig->StepName == 'OrderSetup')
        {
            $result = self::getOrderSetup($UserConfig);
        }
        else if($UserConfig->StepName == 'UserConfig')
        {
            $result = self::getConfigStep($UserConfig);
        }
        else
        {
            if($action == 'userConfig')
            {
                $result = 'User Config is at invalid stage';
            }
            if($action == 'saveConfig')
            {
                $result = ['Error'=>'','StepName'=>'','WizardStepDescription'=>'',
                            'WizardStepTitle'=>'','ConfigItems'=>array()];
            }
            
        }
        return $result;
    }
    
    public static function getApiCredentials($userConfig)
    {
        $result = [
            'Error' => null,
            'StepName' => "AddCredentials",
            'WizardStepTitle' => "Add Credentials",
            'WizardStepDescription' => "This is where you add your website credentials",
            'ConfigItems' => [
                [
                    'ConfigItemId' => "APIKey",
                    'Description' => "Website API Key",
                    'GroupName' => "API Credentials",
                    'MustBeSpecified' => true,
                    'Name' => "API Key",
                    'ReadOnly' => false,
                    'SelectedValue' => $userConfig->ApiKey ?? '',
                    'SortOrder' => 1,
                    'ValueType' => 'PASSWORD',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null

                ],

                [
                    'ConfigItemId' => "APISecretKey",
                    'Description' => "Website API Secret Key",
                    'GroupName' => "API Credentials",
                    'MustBeSpecified' => true,
                    'Name' => "API Secret Key",
                    'ReadOnly' => false,
                    'SelectedValue' => $userConfig->APISecretKey ?? '',
                    'SortOrder' => 2,
                    'ValueType' => 'PASSWORD',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],

                [
                    'ConfigItemId' => "IsOauth",
                    'Description' => "Defines if the authentication type is Oauth",
                    'GroupName' => "API Settings",
                    'MustBeSpecified' => true,
                    'Name' => "Is Oauth",
                    'ReadOnly' => false,
                    'SelectedValue' => ($userConfig->IsOauth==1) ? 'true' : 'false',
                    'SortOrder' => 3,
                    'ValueType' => 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ]
            ],      
        ];

        return $result;
    }

    public static function getOrderSetup($userConfig)
    {
        $result= [
            'Error'=> null,
            'StepName'=> "OrderSetup",
            'WizardStepTitle'=> "Order Setup",
            'WizardStepDescription'=> "Definition of tax settings and items to return",
            'ConfigItems' => [
                [
                    'ConfigItemId'=> "PriceIncTax",
                    'Description'=> "Defines if the price of an item includes tax",
                    'GroupName'=> "Tax",
                    'MustBeSpecified'=> true,
                    'Name'=> "Price Includes Tax",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($userConfig->IsPriceIncTax==1) ? 'true' : 'false',
                    'SortOrder'=> 1,
                    'ValueType'=> 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],

                [
                    'ConfigItemId'=> "DownloadVirtualItems",
                    'Description'=> "Check to allow the download of virtual items",
                    'GroupName'=> "Items",
                    'MustBeSpecified'=> false,
                    'Name'=> "Download Virtual Items",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($userConfig->DownloadVirtualItems==1) ? 'true' : 'false',
                    'SortOrder'=> 2,
                    'ValueType'=> 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ]
            
            ],

        ];

        return $result;
    }

    public static function getConfigStep($userConfig)
    {
        $result= [
            'Error'=> null,
            'StepName'=> "UserConfig",
            'WizardStepTitle'=> "UserConfig",
            'WizardStepDescription'=> "User Config",
            'ConfigItems' => [
                [
                    'ConfigItemId'=> "IsOauth",
                    'Description'=> "Defines if the authentication type is Oauth",
                    'GroupName'=> "Order",
                    'MustBeSpecified'=> true,
                    'Name'=> "Is Oauth",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($userConfig->IsOauth==1) ? 'true' : 'false',
                    'SortOrder'=> 1,
                    'ValueType'=> 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],

                [
                    'ConfigItemId'=> "PriceIncTax",
                    'Description'=> "Defines if the price of an item includes tax",
                    'GroupName'=> "Tax",
                    'MustBeSpecified'=> true,
                    'Name'=> "Price Includes Tax",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($userConfig->IsPriceIncTax==1) ? 'true' : 'false',
                    'SortOrder'=> 2,
                    'ValueType'=> 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],

                [
                    'ConfigItemId'=> "DownloadVirtualItems",
                    'Description'=> "Check to allow the download of virtual items",
                    'GroupName'=> "Items",
                    'MustBeSpecified'=> false,
                    'Name'=> "Download Virtual Items",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($userConfig->DownloadVirtualItems==1) ? 'true' : 'false',
                    'SortOrder'=> 3,
                    'ValueType'=> 'BOOLEAN',
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],

            ],

        ];

        return $result;

    }

    

}
