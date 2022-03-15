<?php

namespace App\Http\Controllers\Linnworks;

use Illuminate\Http\Request;

use App\Models\Linnworks\UserInfo as UserInfo;

class UserInfoAccess
{
    //Check Request with AuthorizationToken
    public static function getUserByToken(Request $request)
    {
        $user = null;
        
        if(!$request->has('AuthorizationToken'))
        {
            return ['Error'=>'Invalid Request!','User'=>$user];
        }

        $token = $request->AuthorizationToken;
        $result = self::loadUserInfo($token);
        
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

    //Load UserInfo from UserInfo Model
    public static function loadUserInfo($token)
    {
        $error = null;
        $user = null;
        
        try{
            $user=UserInfo::where('AuthorizationToken',$token)->first();
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
        }
        return ['Error'=>$error,'User'=>$user];
    }
    
}