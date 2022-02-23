<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Linnworks\UserConfig as UserConfig;
use App\Models\Linnworks\ConfigStage as ConfigStage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ConfigController extends Controller
{


    public function addNewUser(Request $request)
    {
        $error = null;
        $auth_token = null;

        /*
        $validated=$request->validate([
            'LinnworksUniqueIdentifier' => ['required'],
            'Email' => ['required', 'string', 'email', 'max:255', 'unique:linnworks_users'],
            'AccountName' => ['required', 'string'],
        ]);
        */

        $validator=Validator::make($request->all(),[
            'LinnworksUniqueIdentifier' => 'required',
            'Email' => 'required|string|email|max:255|unique:user_configs',
            'AccountName' => 'required|string|unique:user_configs',
        ]);

        if($validator->fails())
        {
            $error = 'Required Field is Empty or Duplicated Email or Account Name!';
            //return $error;
            //return ['Error'=>$error];
        }
        else
        {
            $auth_token = Str::orderedUuid();

            $user = UserConfig::create([
                'UserId' => $request->LinnworksUniqueIdentifier,
                'Email' => $request->Email,
                'AccountName' => $request->AccountName,
                'AuthorizationToken' => $auth_token,
                'IsOauth'=>true,
                'StepName'=>'AddCredentials',
            ]);

            //return ['Error'=>null,'AuthorizationToken'=>$auth_token];
        }
        return ['Error'=>$error,'AuthorizationToken'=>$auth_token];
        //dd($request);
        //return 'Add New User';
    }

    public function userConfig(Request $request)
    {
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!'];
        }

        $token = $request->AuthorizationToken;
        $UserConfig = ConfigStage::loadUserConfig($token);

        if($UserConfig == null)
        {
            return ['Error'=>'User Not Found!'];   
        }

        $response = ConfigStage::ConfigSetUp($UserConfig,'userConfig');
        //$collection = collect($response['ConfigItems'])->where('ConfigItemId',"APIKey")->first()['Description'];
        //dd($collection);
        return json_encode($response);
    }

    public function saveConfig(Request $request)
    {
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!'];
        }

        $token = $request->AuthorizationToken;
        $UserConfig = ConfigStage::loadUserConfig($token);

        if($UserConfig == null)
        {
            return ['Error'=>'User Not Found!'];
        }

        if($request->StepName != $UserConfig->StepName)
        {
            return ['Error'=>'Invalid Step Name Expected ' . $UserConfig->StepName];
        }
        
        
        if ($UserConfig->StepName == "AddCredentials")
        {
            $UserConfig->ApiKey = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"APIKey")->SelectedValue;
            $UserConfig->ApiSecretKey = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"APISecretKey")->SelectedValue;
            $UserConfig->IsOauth = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"IsOauth")->SelectedValue ? 1 : 0;
            $UserConfig->StepName = "OrderSetup";
        }
        else if ($UserConfig->StepName == "OrderSetup")
        {
            $UserConfig->IsPriceIncTax = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"PriceIncTax")->SelectedValue ? 1 : 0;
            $UserConfig->DownloadVirtualItems = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"DownloadVirtualItems")->SelectedValue ? 1 : 0;
            $UserConfig->StepName = "UserConfig";
        }
        else if ($UserConfig->StepName == "UserConfig")
        {
            $UserConfig->IsOauth = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"IsOauth")->SelectedValue ? 1 : 0;
            $UserConfig->IsPriceIncTax = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"PriceIncTax")->SelectedValue ? 1 : 0;
            $UserConfig->DownloadVirtualItems = collect(json_decode($request->ConfigItems))->firstWhere('ConfigItemId',"DownloadVirtualItems")->SelectedValue ? 1 : 0;
        }
        
        $UserConfig->save();

        $response = ConfigStage::ConfigSetUp($UserConfig,'saveConfig');

        return json_encode($response);

    }

    public function shippingTags(Request $request)
    {
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!'];
        }

        $token = $request->AuthorizationToken;
        $UserConfig = ConfigStage::loadUserConfig($token);

        if($UserConfig == null)
        {
            return ['Error'=>'User Not Found!'];
        }
        $response = ConfigStage::getShippingTags();

        return json_encode($response);
    }

    public function paymentTags(Request $request)
    {
        //
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!'];
        }

        $token = $request->AuthorizationToken;
        $UserConfig = ConfigStage::loadUserConfig($token);

        if($UserConfig == null)
        {
            return ['Error'=>'User Not Found!'];
        }
        $response = ConfigStage::getPaymentTags();

        return json_encode($response);
    }

    public function deleted(Request $request)
    {
        //
    }

    public function test(Request $request)
    {
        //
    }

    
}
