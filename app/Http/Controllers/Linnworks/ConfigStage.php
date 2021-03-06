<?php

//namespace App\Models\Linnworks;
namespace App\Http\Controllers\Linnworks;

use App\Models\Linnworks\AppUser as AppUser;

use App\Models\Linnworks\UserConfigSetting as UserConfigSetting;
use App\Models\Linnworks\ShippingTagSetting as ShippingTagSetting;
use App\Models\Linnworks\PaymentTagSetting as PaymentTagSetting;

class ConfigStage
{
    /**
         * Provide form for each step name
         * @param $user - App User
         * @param $caller - The caller from userConfig or saveConfig function
         * @return "App\Models\Linnworks\UserConfigSetting for different step name"
    */
    public static function ConfigSetUp($user,$caller)
    {
        $result=null;

        if($user->StepName == 'AddCredentials')
        {
            $result = self::getApiCredentials($user);
        }
        else if($user->StepName == 'OrderSetup')
        {
            $result = self::getOrderSetup($user);
        }
        else if($user->StepName == 'UserConfig')
        {
            $result = self::getConfigStep($user);
        }
        else
        {
            if($caller == 'userConfig')
            {
                $result = 'User Config is at invalid stage';
            }
            if($caller == 'saveConfig')
            {
                $result = ['Error'=>'','StepName'=>'','WizardStepDescription'=>'',
                            'WizardStepTitle'=>'','ConfigItems'=>array()];
            }
            
        }
        return $result;
    }

    /**
         * Create shipping tags with friendly names and their tags in Linnworks config wizard for user to config
         * @return "App\Models\Linnworks\ShippingTagSetting"
    */
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

    /**
         * Create payment tags with friendly names and their tags in Linnworks config wizard for user to config
         * @return "App\Models\Linnworks\PaymentTagSetting"
    */
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
    
    /**
         * Credential Form for user to fill out 
         * @param $user - App User
         * @return "App\Models\Linnworks\UserConfigSetting"
    */
    public static function getApiCredentials($user)
    {
        $setting = new UserConfigSetting();

        $setting->UserConfig = [
            'Error' => null,
            'StepName' => "AddCredentials",
            'WizardStepTitle' => "Add Credentials",
            'WizardStepDescription' => "This is where you add your website credentials",
            'ConfigItems' => [$setting->ConfigItem = 
                [
                    'ConfigItemId' => "APIKey",
                    'Description' => "Website API Key",
                    'GroupName' => "API Credentials",
                    //'MustBeSpecified' => true,
                    'MustBeSpecified' => false,
                    'Name' => "API Key",
                    'ReadOnly' => false,
                    'SelectedValue' => $user->ApiKey ?? '',
                    'SortOrder' => 1,
                    'ValueType' => $setting->ConfigValueType['Password'],
                    
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null

                ],
                $setting->ConfigItem =
                [
                    'ConfigItemId' => "APISecretKey",
                    'Description' => "Website API Secret Key",
                    'GroupName' => "API Credentials",
                    //'MustBeSpecified' => true,
                    'MustBeSpecified' => false,
                    'Name' => "API Secret Key",
                    'ReadOnly' => false,
                    'SelectedValue' => $user->APISecretKey ?? '',
                    'SortOrder' => 2,
                    'ValueType' => $setting->ConfigValueType['Password'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],
                $setting->ConfigItem =
                [
                    'ConfigItemId' => "IsOauth",
                    'Description' => "Defines if the authentication type is Oauth",
                    'GroupName' => "API Settings",
                    //'MustBeSpecified' => true,
                    'MustBeSpecified' => false,
                    'Name' => "Is Oauth",
                    'ReadOnly' => false,
                    'SelectedValue' => ($user->IsOauth==1) ? 'true' : 'false',
                    'SortOrder' => 3,
                    'ValueType' => $setting->ConfigValueType['Boolean'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null

                ],
                $setting->ConfigItem =
                [
                    'ConfigItemId' => "Version",
                    'Description' => "Version of the API",
                    'GroupName' => "API Settings",
                    'MustBeSpecified' => true,
                    //'MustBeSpecified' => false,
                    'Name' => "Is Oauth",
                    'ReadOnly' => false,
                    'SelectedValue' => ($user->IsOauth==1) ? 'true' : 'false',
                    'SortOrder' => 3,
                    'ValueType' => $setting->ConfigValueType['List'],
                    'ListValues'=>[
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1',
                            'Value' => '1'
                        ],
                        /*
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1.1',
                            'Value' => '1.1'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1.2',
                            'Value' => '1.2'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1.3',
                            'Value' => '1.3'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1.4',
                            'Value' => '1.4'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 1.5',
                            'Value' => '1.5'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 2.1',
                            'Value' => '2.1'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 2.2',
                            'Value' => '2.2'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 2.3',
                            'Value' => '2.3'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 2.4',
                            'Value' => '2.4'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 2.5',
                            'Value' => '2.5'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 3.1',
                            'Value' => '3.1'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 3.2',
                            'Value' => '3.2'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 3.3',
                            'Value' => '3.3'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 3.4',
                            'Value' => '3.4'
                        ],
                        $setting->ListValue =
                        [
                            'Display'=>'Version 3.5',
                            'Value' => '3.5'
                        ],
                        */
                    ],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                    
                ]
            ],      
        ];
        return $setting->UserConfig;
    }

    /**
         * Order Form for user to fill out  
         * @param $user - App User
         * @return "App\Models\Linnworks\UserConfigSetting"
    */
    public static function getOrderSetup($user)
    {
        $setting = new UserConfigSetting();

        $setting->UserConfig= [
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
                    'SelectedValue'=> ($user->IsPriceIncTax==1) ? 'true' : 'false',
                    'SortOrder'=> 1,
                    'ValueType'=> $setting->ConfigValueType['Boolean'],
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
                    'SelectedValue'=> ($user->DownloadVirtualItems==1) ? 'true' : 'false',
                    'SortOrder'=> 2,
                    'ValueType'=> $setting->ConfigValueType['Boolean'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ]
            ],
        ];
        return $setting->UserConfig;
    }

    /**
         * Config Form for user to fill out 
         * @param $user - App User
         * @return "App\Models\Linnworks\UserConfigSetting"
    */
    public static function getConfigStep($user)
    {
        $setting = new UserConfigSetting();

        $setting->UserConfig= [
            'Error'=> null,
            'StepName'=> "UserConfig",
            'WizardStepTitle'=> "UserConfig",
            'WizardStepDescription'=> "User Config",
            'ConfigItems' => [
                $setting->ConfigItem = 
                [
                    'ConfigItemId'=> "SellerId",
                    'Description' => 'Enter the seller Id',
                    'GroupName'=>'Account',

                    'MustBeSpecified'=> false,
                    'Name'=> "Seller Id",
                    'ReadOnly'=> false,
                    'SelectedValue'=> $user->UserId ?? '',
                    'SortOrder'=> 1,
                    'ValueType'=> $setting->ConfigValueType['Guid'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],
                $setting->ConfigItem = 
                [
                    'ConfigItemId'=> "Marketplace",
                    'Description' => 'Channel Name',
                    'GroupName'=>'Account',

                    'MustBeSpecified'=> false,
                    'Name'=> "Marketplace",
                    'ReadOnly'=> false,
                    'SelectedValue'=> 'DISCOGSINTEGRATION',
                    'SortOrder'=> 1,
                    'ValueType'=> $setting->ConfigValueType['String'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],
                /*
                $setting->ConfigItem =
                [
                    'ConfigItemId'=> "IsOauth",
                    'Description'=> "Defines if the authentication type is Oauth",
                    'GroupName'=> "Order",
                    'MustBeSpecified'=> true,
                    'Name'=> "Is Oauth",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($user->IsOauth==1) ? 'true' : 'false',
                    'SortOrder'=> 1,
                    'ValueType'=> $setting->ConfigValueType['Boolean'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],
                */
                $setting->ConfigItem =
                [
                    'ConfigItemId'=> "PriceIncTax",
                    'Description'=> "Defines if the price of an item includes tax",
                    'GroupName'=> "Tax",
                    'MustBeSpecified'=> true,
                    'Name'=> "Price Includes Tax",
                    'ReadOnly'=> false,
                    'SelectedValue'=> ($user->IsPriceIncTax==1) ? 'true' : 'false',
                    'SortOrder'=> 2,
                    'ValueType'=> $setting->ConfigValueType['Boolean'],
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
                    'SelectedValue'=> ($user->DownloadVirtualItems==1) ? 'true' : 'false',
                    'SortOrder'=> 3,
                    'ValueType'=> $setting->ConfigValueType['Boolean'],
                    'ListValues'=>[],
                    'RegExValidation'=>null,
                    'RegExError'=>null
                ],
            ],
        ];
        return $setting->UserConfig;
    }
}
