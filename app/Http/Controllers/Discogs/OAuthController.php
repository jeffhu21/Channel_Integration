<?php

namespace App\Http\Controllers\discogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Psr7;
/*
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
*/
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Discogs\SendRequest as SendRequest;
use App\Models\OauthToken;
use App\Models\User;
use App\Models\AppKey;
use App\Models\AppUser;

use App\Mail\DiscogsAuthentication;

class OAuthController extends Controller
{

    /**
         * Make request to Discogs for request token
         * @param $app_user_id - App\Models\AppUser id
    */
    public static function requestToken($app_user_id)
    {
        $dir = 'oauth/request_token';

        $res = SendRequest::httpRequest('GET',$dir);
      
        $msg=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $msg = $res['Error'];
            return ["Error"=>$msg,"Response"=>$stream];
        }
        
        $stream = Psr7\Query::parse($res['Response']->getBody()->getContents());
        $oauth_token = $stream['oauth_token']; //Temporary Token
        $oauth_token_secret = $stream['oauth_token_secret']; //Temporary Token Secret
        
        if($app_user_id != null)
        {
            self::saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret); //Save Temporary Token
        }
        
        return self::oauthAuthorize($oauth_token);

    }

    /**
         * Redirect to outside domain(Discogs) to get the authorization from user
         * @param $oauth_token - Temporary token from the requestToken function
    */
    public static function oauthAuthorize($oauth_token)
    { 
        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $oauth_token; 

        return redirect()->away($dir);
    }

    /**
         * Handle Callback URL from Discogs to get verifier
         * @param Request $request - oauth_verifier,oauth_token
         * @return 'message'
    */
    public static function getVerifier(Request $request)
    {  
        $oauth_verifier=$request->oauth_verifier;  //Verifier Authorized by User
        $oauth_token=$request->oauth_token;

        $row = OauthToken::firstWhere('oauth_token', $oauth_token);

        $app=$row->update([
            'oauth_verifier'=>$oauth_verifier
        ]); 

        self::accessToken($row->app_user_id);

        return 'Authentication Successful!';
    }

    /**
         * Get the permanent token from Discogs through authorization by user
         * @param $app_user_id - App\Models\AppUser id
    */
    public static function accessToken($app_user_id)
    {       
        $res = SendRequest::httpRequest('GET','oauth/access_token',false,'',$app_user_id);
        
        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Response"=>$stream];
        }

        $stream = Psr7\Query::parse($res['Response']->getBody()->getContents());
        $oauth_token = $stream['oauth_token']; //Permanent Token
        $oauth_token_secret = $stream['oauth_token_secret']; //Permanent Token Secret

        //$row = OauthToken::firstWhere('app_user_id', $app_user_id);

        //echo('Row = '.$row->oauth_token.', '.$row->oauth_verifier);

        self::saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret);
    }

    /**
         * Handle web route to get the oauth tokens for the app user by email
         * @param Request $request - form data
    */
    public static function DiscogsOauth(Request $request)
    {
        $app_user_email = $request['app_user_email'];

        $msg = null;
        
        if($app_user_email != null)
        {

            $app = AppUser::firstWhere('Email',$app_user_email);

            if($app != null)
            {
                $app_user_id = $app->id;

                return self::requestToken($app_user_id);
            }
            else
            {
                $msg = 'Email Not Found!';

                return view('/DiscogsOauth',['message'=>$msg]);
            }
            
        }

    }
    
    /**
         * Handle api route to get the oauth tokens by App User clicking authenticate button in the received email
         * @param Request $request - form data with id
    */
    public static function APIDiscogsOauth(Request $request)
    {

        return self::requestToken($request->id);
        
    }
    
    /**
         * Get username in Discogs
         * @param $app_user_id - App\Models\AppUser id
         * @return [String:$error,HttpResponse->Username]
    */
    public static function getUsername($app_user_id)
    {
        $dir = 'oauth/identity';

        $res = SendRequest::httpRequest('GET',$dir,true,'',$app_user_id);

        $error=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Username"=>null];
        }

        $stream=json_decode($res['Response']->getBody()->getContents());

        return ["Error"=>$error,"Username"=>$stream->username];
  
    }

    /**
         * Get User Account in Discogs
         * @param $app_user_id - App\Models\AppUser id
         * @return [String:$error,HttpResponse]
    */
    public static function getIdentity($app_user_id)
    {
        $dir = 'oauth/identity';

        $res = SendRequest::httpRequest('GET',$dir,true,'',$app_user_id);

        $error=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Stream"=>null];
        }

        $stream=json_decode($res['Response']->getBody()->getContents());

        return ["Error"=>$error,"Stream"=>$stream];
    }

    /**
         * Create or update the oauth token in database
         * @param $app_user_id - App\Models\AppUser id
         * @param $oauth_token - App\Models\OauthToken oauth token
         * @param $oauth_token_secret - App\Models\OauthToken oauth token secret
    */
    public static function saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret)
    {
        $record = OauthToken::firstWhere('app_user_id',$app_user_id);

        $app_user = AppUser::firstWhere('id',$app_user_id);

        if($record != null)
        {
            $app=$app_user->OauthToken()->update([
                'oauth_token'=>$oauth_token,
                'oauth_secret'=>$oauth_token_secret,
                //'oauth_verifier'=>$oauth_verifier
            ]);
        }
        else
        {
            $app=$app_user->OauthToken()->create([
                'oauth_token'=>$oauth_token,
                'oauth_secret'=>$oauth_token_secret,
                //'oauth_verifier'=>$oauth_verifier
            ]);
        }

    }

    /**
         * Save Consumer Key and Secret in Application created in Discogs and Application Id and Secret in Application created in Linnworks
         * @param Request $request - form data
         * @return 'home page'
    */
    public function saveAppKey(Request $request)
    {
        $user_id = AppKey::firstWhere('user_id',Auth::user()->id);

        if($user_id == null)
        {
            $app=Auth::user()->appKey()->create([
                'discogs_consumer_key'=> $request['consumer_key'],
                'discogs_consumer_secret'=> $request['consumer_secret'],
                'linnworks_application_id'=>$request['application_id'],
                'linnworks_application_secret'=> $request['application_secret'],
                'callback_url'=>$request['callback_url'].'/oauth_verifier'
            ]);
        }
        else
        {
            $app=Auth::user()->appKey()->update([
                //'user_id',
                'discogs_consumer_key'=> $request['consumer_key'],
                'discogs_consumer_secret'=> $request['consumer_secret'],
                'linnworks_application_id'=>$request['application_id'],
                'linnworks_application_secret'=> $request['application_secret'],
                'callback_url'=>$request['callback_url'].'/oauth_verifier'
            ]);
        }

        return view('home');
    }
    
}
