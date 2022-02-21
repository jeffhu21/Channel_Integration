<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Linnworks\ConfigItem as ConfigItem;
use App\Models\Linnworks\UserConfig as UserConfig;
//use App\Models\Linnworks\UserConfigItem as UserConfigItem;
use App\Models\Linnworks\UserConfigResponse as UserConfigResponse;
use App\Models\Linnworks\ConfigStage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ConfigController extends Controller
{
    
    public function ConfigStage(UserConfig $UserConfig)
    {
        $response=null;

        if($UserConfig['StepName'] == 'AddCredentials')
        {
            $response = getApiCredentials($UserConfig);
        }
        else if($UserConfig['StepName'] == 'OrderSetup')
        {
            $response = getOrderSetup($UserConfig);
        }
        else if($UserConfig['StepName'] == 'UserConfig')
        {
            $response = getConfigStep($UserConfig);
        }
        else
        {
            $response = 'User Config is at invalid stage';
        }
        return $response;
    }


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
        $error = null;
        $response = null;

        if($request->has('AuthorizationToken'))
        {
            $token = $request->AuthorizationToken;
            $UserConfig=UserConfig::findOrFail($token);

            if($UserConfig != null)
            {
                $response = ConfigStage($UserConfig);
            }
            else
            {
                $error = 'User Not Found!';
            }
        }
        else
        {
            $error = 'Invalid Request!';
        }

        return ['Error'=>$error,'Response'=>$response];

        //dd(session('IsOauth'));
        //echo(session('IsOauth') . ', ' . session('StepName') . '<br>');
        //return 'User Config';
    }

    public function saveConfig()
    {
        return 'Save Config';
    }

    public function shippingTags()
    {

    }

    public function paymentTags()
    {
        
    }

    public function deleted()
    {
        
    }

    public function test()
    {
        
    }

    
}
