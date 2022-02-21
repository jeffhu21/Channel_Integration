<?php

namespace App\Models\Linnworks;

use App\Models\Linnworks\ConfigItem as ConfigItem;
use App\Models\Linnworks\UserConfig as UserConfig;
use App\Models\Linnworks\UserConfigResponse as UserConfigResponse;

class ConfigStage
{
    
    public static function getApiCredentials($userConfig)
    {
        $response = new UserConfigResponse();

        //$Error = null;

        //$response->Error = $Error;
        $response->StepName='AddCredentials';
        $response->WizardStepTitle='Add Credentials';
        $response->WizardStepDescription='This is where you add your website credentials';
        
        $response->ConfigItems[0] = new ConfigItem();
        $response->ConfigItems[0]->ConfigItemId = "APIKey";
        $response->ConfigItems[0]->Description = "Website API Key";
        $response->ConfigItems[0]->GroupName = "API Credentials";
        $response->ConfigItems[0]->MustBeSpecified = true;
        $response->ConfigItems[0]->Name = "API Key";
        $response->ConfigItems[0]->ReadOnly = false;
        $response->ConfigItems[0]->SelectedValue = $userConfig->ApiKey ?? '';
        $response->ConfigItems[0]->SortOrder = 1;
        $response->ConfigItems[0]->ValueType = $response->ConfigItems[0]->GetConfigValueType('tPassword');
        
        $response->ConfigItems[1] = new ConfigItem();
        $response->ConfigItems[1]->ConfigItemId = "APISecretKey";
        $response->ConfigItems[1]->Description = "Website API Secret Key";
        $response->ConfigItems[1]->GroupName = "API Credentials";
        $response->ConfigItems[1]->MustBeSpecified = true;
        $response->ConfigItems[1]->Name = "API Secret Key";
        $response->ConfigItems[1]->ReadOnly = false;
        $response->ConfigItems[1]->SelectedValue = $userConfig->APISecretKey ?? '';
        $response->ConfigItems[1]->SortOrder = 2;
        $response->ConfigItems[1]->ValueType = $response->ConfigItems[1]->GetConfigValueType('tPassword');
        
        $response->ConfigItems[2] = new ConfigItem();
        $response->ConfigItems[2]->ConfigItemId = "IsOauth";
        $response->ConfigItems[2]->Description = "Defines if the authentication type is Oauth";
        $response->ConfigItems[2]->GroupName = "API Settings";
        $response->ConfigItems[2]->MustBeSpecified = true;
        $response->ConfigItems[2]->Name = "Is Oauth";
        $response->ConfigItems[2]->ReadOnly = false;
        $response->ConfigItems[2]->SelectedValue = ($userConfig->IsOauth==1) ? 'true' : 'false';
        $response->ConfigItems[2]->SortOrder = 3;
        $response->ConfigItems[2]->ValueType = $response->ConfigItems[2]->GetConfigValueType('tBool');
            
        return $response;
    }

    public static function getOrderSetup($userConfig)
    {
        $response = new UserConfigResponse();

        $Error = 'null';

        $response->Error = $Error;
        $response->StepName = "OrderSetup";
        $response->WizardStepTitle = "Order Setup";
        $response->WizardStepDescription = "Definition of tax settings and items to return";

        $response->ConfigItems[0] = new ConfigItem();
        $response->ConfigItems[0]->ConfigItemId = "PriceIncTax";
        $response->ConfigItems[0]->Description = "Defines if the price of an item includes tax";
        $response->ConfigItems[0]->GroupName = "Tax";
        $response->ConfigItems[0]->MustBeSpecified = true;
        $response->ConfigItems[0]->Name = "Price Includes Tax";
        $response->ConfigItems[0]->ReadOnly = false;
        $response->ConfigItems[0]->SelectedValue = ($userConfig->IsPriceIncTax==1) ? 'true' : 'false';
        $response->ConfigItems[0]->SortOrder = 1;
        $response->ConfigItems[0]->ValueType = $response->ConfigItems[0]->ConfigValueType->BOOLEAN;

        $response->ConfigItems[1] = new ConfigItem();
        $response->ConfigItems[1]->ConfigItemId = "DownloadVirtualItems";
        $response->ConfigItems[1]->Description = "Check to allow the download of virtual items";
        $response->ConfigItems[1]->GroupName = "Items";
        $response->ConfigItems[1]->MustBeSpecified = false;
        $response->ConfigItems[1]->Name = "Download Virtual Items";
        $response->ConfigItems[1]->ReadOnly = false;
        $response->ConfigItems[1]->SelectedValue = ($userConfig->DownloadVirtualItems==1) ? 'true' : 'false';
        $response->ConfigItems[1]->SortOrder = 2;
        $response->ConfigItems[1]->ValueType = $response->ConfigItems[1]->ConfigValueType->BOOLEAN;

        return $response;
    }

    public static function getConfigStep($userConfig)
    {
        $response = new UserConfigResponse();

        $Error = 'null';

        $response->Error = $Error;
        $response->StepName = "UserConfig";
        $response->WizardStepTitle = "UserConfig";
        $response->WizardStepDescription = "User Config";

        $response->ConfigItems[0] = new ConfigItem();
        $response->ConfigItems[0]->ConfigItemId = "IsOauth";
        $response->ConfigItems[0]->Description = "Defines if the authentication type is Oauth";
        $response->ConfigItems[0]->GroupName = "Order";
        $response->ConfigItems[0]->MustBeSpecified = true;
        $response->ConfigItems[0]->Name = "Is Oauth";
        $response->ConfigItems[0]->ReadOnly = false;
        $response->ConfigItems[0]->SelectedValue = ($userConfig->IsOauth==1) ? 'true' : 'false';
        $response->ConfigItems[0]->SortOrder = 1;
        $response->ConfigItems[0]->ValueType = $response->ConfigItems[0]->ConfigValueType->BOOLEAN;

        $response->ConfigItems[1] = new ConfigItem();
        $response->ConfigItems[1]->ConfigItemId = "PriceIncTax";
        $response->ConfigItems[1]->Description = "Defines if the price of an item includes tax";
        $response->ConfigItems[1]->GroupName = "Tax";
        $response->ConfigItems[1]->MustBeSpecified = true;
        $response->ConfigItems[1]->Name = "Price Includes Tax";
        $response->ConfigItems[1]->ReadOnly = false;
        $response->ConfigItems[1]->SelectedValue = ($userConfig->IsPriceIncTax==1) ? 'true' : 'false';
        $response->ConfigItems[1]->SortOrder = 2;
        $response->ConfigItems[1]->ValueType = $response->ConfigItems[1]->ConfigValueType->BOOLEAN;


        $response->ConfigItems[2] = new ConfigItem();
        $response->ConfigItems[2]->ConfigItemId = "DownloadVirtualItems";
        $response->ConfigItems[2]->Description = "Check to allow the download of virtual items";
        $response->ConfigItems[2]->GroupName = "Items";
        $response->ConfigItems[2]->MustBeSpecified = false;
        $response->ConfigItems[2]->Name = "Download Virtual Items";
        $response->ConfigItems[2]->ReadOnly = false;
        $response->ConfigItems[2]->SelectedValue = ($userConfig->DownloadVirtualItems==1) ? 'true' : 'false';
        $response->ConfigItems[2]->SortOrder = 3;
        $response->ConfigItems[2]->ValueType = $response->ConfigItems[2]->ConfigValueType->BOOLEAN;

        return $response;

    }

    

}
