<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Linnworks\UserConfig as UserConfig;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ConfigController extends Controller
{
    
    public function getUserConfigs($token)
    {
        $config=UserConfig::findOrFail($token);
        $response=null;

        if($config['step_name'] == 'AddCredentials')
        {
            $response = getApiCredentials();
        }
        else if($config['step_name'] == 'OrderSetup')
        {
            $response = getOrderSetup();
        }
        else if($config['step_name'] == 'UserConfig')
        {
            $response = getConfigStep();
        }
        else
        {
            $response = 'User Config is at invalid stage';
        }
        return $response;
    }

    public function getApiCredentials()
    {
        
    }

    public function getOrderStep()
    {
        
    }

    public function getConfigStep()
    {
        
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
            'AccountName' => 'required|string',
        ]);

        if($validator->fails())
        {
            $error = 'Required Field is Empty or Duplicated Email!';
            //return $error;
            //return ['Error'=>$error];
        }
        else
        {
            $auth_token = Str::orderedUuid();

            $user = UserConfig::create([
                'user_id' => $request->LinnworksUniqueIdentifier,
                'email' => $request->Email,
                'account_name' => $request->AccountName,
                'authorization_token' => $auth_token,
                'is_oauth'=>true,
                'step_name'=>'AddCredentials',
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

        if($request->has('AuthorizationToken'))
        {
            
        }
        else
        {
            $error = 'User Not Found!';
        }

        return ['Error'=>$error];

        //dd(session('is_oauth'));
        //echo(session('is_oauth') . ', ' . session('step_name') . '<br>');
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
