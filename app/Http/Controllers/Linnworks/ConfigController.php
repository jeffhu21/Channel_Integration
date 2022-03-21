<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Http\Controllers\Discogs\OAuthController as OAuthController;
use Illuminate\Http\Request;


use App\Models\Linnworks\AppUser as AppUser;
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
            'Email' => 'required|string|email|max:255|unique:user_infos',
            'AccountName' => 'required|string|unique:user_infos',
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
            $user = AppUser::create([
                'UserId' => $request->LinnworksUniqueIdentifier,
                'Email' => $request->Email,
                'AccountName' => $request->AccountName,
                'AuthorizationToken' => $auth_token,
                'IsOauth'=>true,
                'StepName'=>'AddCredentials',
            ]);

            //return ['Error'=>null,'AuthorizationToken'=>$auth_token];
        }

        //dd(response()->json(['Error'=>$error,'AuthorizationToken'=>$auth_token]));
        //dd(strlen(json_encode(['Error'=>$error,'AuthorizationToken'=>$auth_token])));
        
        return SendResponse::httpResponse(['Error'=>$error,'AuthorizationToken'=>$auth_token]);


        //dd($request);
    }

    public function userConfig(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];

        OAuthController::DiscogsOauth($app_user->id); //Discogs Authentication

        $response = ConfigStage::ConfigSetUp($app_user,'userConfig');
        //$collection = collect($response['ConfigItems'])->where('ConfigItemId',"APIKey")->first()['Description'];
        //dd($collection);

        //return json_encode($response);
        return SendResponse::httpResponse($response);
    }

    public function saveConfig(Request $request)
    {
        //dd(json_decode($request->query()['ConfigItems']));
        //dd('SaveConfig: '.json_encode($request->query()['ConfigItems']));
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        if($request->StepName != $user->StepName)
        {
            return ['Error'=>'Invalid Step Name Expected ' . $user->StepName];
        }
        
        if ($user->StepName == "AddCredentials")
        {
            $user->ApiKey = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId','APIKey')['SelectedValue'];
             $user->ApiSecretKey = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"APISecretKey")['SelectedValue'];
             $user->IsOauth = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"IsOauth")['SelectedValue'] ? 1 : 0;
            
            //dd("APIKey: ".$user->ApiKey.", APISecretKey: ".$user->ApiSecretKey.", IsOauth: ".$user->IsOauth);
            $user->StepName = "OrderSetup";
        }
        else if ($user->StepName == "OrderSetup")
        {
            $user->IsPriceIncTax = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"PriceIncTax")['SelectedValue'] ? 1 : 0;
             $user->DownloadVirtualItems = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"DownloadVirtualItems")['SelectedValue'] ? 1 : 0;
            $user->StepName = "UserConfig";
        }
        else if ($user->StepName == "UserConfig")
        {
            $user->IsOauth = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"IsOauth")['SelectedValue'] ? 1 : 0;
             $user->IsPriceIncTax = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"PriceIncTax")['SelectedValue'] ? 1 : 0;
             $user->DownloadVirtualItems = collect($request->input('ConfigItems'))->firstWhere('ConfigItemId',"DownloadVirtualItems")['SelectedValue'] ? 1 : 0;
        }
        
        $user->save();

        $response = ConfigStage::ConfigSetUp($user,'saveConfig');

        //return json_encode($response);
        return SendResponse::httpResponse($response);

    }

    public function shippingTags(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        $response = ConfigStage::getShippingTags();

        //return json_encode($response);
        return SendResponse::httpResponse($response);
    }

    public function paymentTags(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];
        
        $response = ConfigStage::getPaymentTags();

        //return json_encode($response);
        return SendResponse::httpResponse($response);
    }

    public function deleted(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        $error = null;

        try
        {
            AppUser::where('AuthorizationToken',$request->AuthorizationToken)->delete();
            $error = 'User config does not exist';
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        //return ['Error'=>$error];
        return SendResponse::httpResponse(['Error'=>$error]);
    }

    public function test(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        $error = null;

        try
        {
            //Would normally do some test here

            if($user->StepName == "UserConfig")
            {
                $error = null;
            }
            else
            {
                $error = 'Config Not Finished!';
            }

            //$error = 'User config does not exist';
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        //return ['Error'=>$error];
        return SendResponse::httpResponse(['Error'=>$error]);
    }

    
}
