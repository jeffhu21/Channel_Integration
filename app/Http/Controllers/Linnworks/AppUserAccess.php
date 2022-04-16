<?php

namespace App\Http\Controllers\Linnworks;

use Illuminate\Http\Request;

use App\Models\AppUser as AppUser;

class AppUserAccess
{
    /**
         * Check Request with AuthorizationToken and retrieve the user
         * @param Request $request - with AuthorizationToken
         * @return [String: Error, AppUser: $user]
    */
    public static function getUserByToken(Request $request)
    {
        $user = null;
        
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!','User'=>$user];
        }

        $token = $request->AuthorizationToken;
        $result = self::loadAppUser($token);
        
        if($result['Error'] != null)
        {
            return ['Error'=>$result['Error'],'User'=>$result['User']];
        }
        $user = $result['User'];

        if($user == null)
        {
            return ['Error'=>'User Not Found!','User'=>$result['User']];   
        }
        return ['Error' => null,'User'=>$user];
    }

    /**
         * Retrieve AppUser from database
         * @param $token - Authorization Token
         * @return [String: $error, AppUser: $user]
    */
    public static function loadAppUser($token)
    {
        $error = null;
        $user = null;
        
        try{
            $user=AppUser::where('AuthorizationToken',$token)->first();
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        return ['Error'=>$error,'User'=>$user];
    }
    
}