<?php

namespace App\Models\Linnworks;

//use App\Models\Linnworks\ConfigItem as ConfigItem;
use App\Models\Linnworks\UserConfig as UserConfig;
//use App\Models\Linnworks\UserConfigResponse as UserConfigResponse;
use App\Models\Linnworks\ConfigStageNotUsed as ConfigStageNotUsed;

use App\Models\Linnworks\UserConfigSetting as UserConfigSetting;
use App\Models\Linnworks\ShippingTagSetting as ShippingTagSetting;
use App\Models\Linnworks\PaymentTagSetting as PaymentTagSetting;

class ConfigStage
{

    //Load UserConfig from UserConfig Model
    public static function loadUserConfig($token)
    {
        $error = null;
        $UserConfig = null;
        
        try{
            $UserConfig=UserConfig::where('AuthorizationToken',$token)->first();
            //return $UserConfig;
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        return ['Error'=>$error,'UserConfig'=>$UserConfig];
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

    public static function getShippingTags()
    {
        $setting = new ShippingTagSetting();

        $setting->response = [
            'Error' => null,
            'ShippingTags'=>[
            $setting->ShippingTag=[
                'Tag'=>'FedEx',
                'FriendlyName'=>'FedEx',
                'Site'=>''
            ],
            $setting->ShippingTag =[
                'Tag'=>'UpS',
                'FriendlyName'=>'UpS',
                'Site'=>''
            ],
            $setting->ShippingTag =[
                'Tag'=>'DHL',
                'FriendlyName'=>'DHL',
                'Site'=>''
            ],
            $setting->ShippingTag =[
                'Tag'=>'Purolator',
                'FriendlyName'=>'Purolator',
                'Site'=>''
            ]

            ]
        ];
        return $setting->response;
    }

    public static function getPaymentTags()
    {
        $setting = new PaymentTagSetting();

        $setting->response = [
            'Error' => null,
            'PaymentTags'=>[
            $setting->PaymentTag=[
                'Tag'=>'paypal_verified',
                'FriendlyName'=>'PayPal',
                'Site'=>''
            ],
            $setting->PaymentTag =[
                'Tag'=>'mastercard',
                'FriendlyName'=>'Credit Card - Master Card',
                'Site'=>''
            ],
            $setting->PaymentTag =[
                'Tag'=>'visa_credit',
                'FriendlyName'=>'Credit Card - Visa',
                'Site'=>''
            ],
            $setting->PaymentTag =[
                'Tag'=>'american_express',
                'FriendlyName'=>'Credit Card - American Express',
                'Site'=>''
            ],
            $setting->PaymentTag =[
                'Tag'=>'bank',
                'FriendlyName'=>'Bank payments',
                'Site'=>''
            ]

            ]
        ];
        return $setting->response;
    }
    
    public static function getApiCredentials($userConfig)
    {
        $setting = new UserConfigSetting();

        $setting->response = [
            'Error' => null,
            'StepName' => "AddCredentials",
            'WizardStepTitle' => "Add Credentials",
            'WizardStepDescription' => "This is where you add your website credentials",
            'ConfigItems' => [$setting->ConfigItem = 
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
                $setting->ConfigItem =
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
                $setting->ConfigItem =
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
        return $setting->response;
    }

    public static function getOrderSetup($userConfig)
    {
        $setting = new UserConfigSetting();

        $setting->response= [
            'Error'=> null,
            'StepName'=> "OrderSetup",
            'WizardStepTitle'=> "Order Setup",
            'WizardStepDescription'=> "Definition of tax settings and items to return",
            'ConfigItems' => [$setting->ConfigItem =
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
                $setting->ConfigItem =
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
        return $setting->response;
    }

    public static function getConfigStep($userConfig)
    {
        $setting = new UserConfigSetting();

        $setting->response= [
            'Error'=> null,
            'StepName'=> "UserConfig",
            'WizardStepTitle'=> "UserConfig",
            'WizardStepDescription'=> "User Config",
            'ConfigItems' => [$setting->ConfigItem =
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
                $setting->ConfigItem =
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
                $setting->ConfigItem =
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
        return $setting->response;
    }
}
