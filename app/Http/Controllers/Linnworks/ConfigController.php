<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;

use App\Models\AppUser as AppUser;
use App\Http\Controllers\Linnworks\ConfigStage as ConfigStage;
use App\Mail\DiscogsAuthentication;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;


class ConfigController extends Controller
{
    /**
         * Add AppUser into database when the seller firstly installs the channel integration
         * Send email to AppUser to do the authentication required by Discogs
         * @param Request $request - with LinnworksUniqueIdentifier, Email, AccountName
         * @return [String: $error, String: $auth_token]
    */
    public function addNewUser(Request $request)
    {
        $error = null;
        $auth_token = null;

        $validator=Validator::make($request->all(),[
            'LinnworksUniqueIdentifier' => 'required',
            'Email' => 'required|string|email|max:255|unique:app_users',
            'AccountName' => 'required|string|unique:app_users',
        ]);

        if($validator->fails())
        {
            $error = 'Required Field is Empty or Duplicated Email or Account Name!';
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
            //Send Email to App User for Discogs Authentication
            //Mail::to($user->Email)->send(new DiscogsAuthentication($user->id));
        }
        
        return SendResponse::httpResponse(['Error'=>$error,'AuthorizationToken'=>$auth_token]);
    }

    /**
         * Provide Config forms for user to complete according to Linnworks wizard
         * @param Request $request - with AuthorizationToken
         * @return [String: $error, String: StepName, String AccountName, String WizardStepDescription, String WizardStepTitle, ConfigItems[]]
    */
    public function userConfig(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];

        $response = ConfigStage::ConfigSetUp($app_user,'userConfig');
      
        return SendResponse::httpResponse($response);
    }

    /**
         * Update and Save the Config Information of user
         * @param Request $request - with AuthorizationToken, ConfigItems[], StepName
         * @return [String: $error, String: AuthorizationToken, String: StepName, String WizardStepDescription, String WizardStepTitle, ConfigItems[]]
    */
    public function saveConfig(Request $request)
    {
        
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

        return SendResponse::httpResponse($response);

    }

    /**
         * Pre-populate a list of shipping tags in Linnworks config wizard
         * @param Request $request - with AuthorizationToken
         * @return [String: $error, ShippingTags[]]
    */
    public function shippingTags(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        $response = ConfigStage::getShippingTags();

        return SendResponse::httpResponse($response);
    }

    /**
         * Pre-populate a list of payment tags in Linnworks config wizard
         * @param Request $request - with AuthorizationToken
         * @return [String: $error, PaymentTags[]]
    */
    public function paymentTags(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];
        
        $response = ConfigStage::getPaymentTags();

        return SendResponse::httpResponse($response);
    }

    /**
         * Remove the app user from database 
         * @param Request $request - with AuthorizationToken
         * @return [String: $error]
    */
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
        return SendResponse::httpResponse(['Error'=>$error]);
    }

    /**
         * Test the customer's integration is valid
         * @param Request $request - with AuthorizationToken
         * @return [String: $error]
    */
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

            
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        
        return SendResponse::httpResponse(['Error'=>$error]);
    }

    
}
