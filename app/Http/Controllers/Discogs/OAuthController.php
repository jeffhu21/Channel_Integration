<?php

namespace App\Http\Controllers\discogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Discogs\SendRequest as SendRequest;

use App\Models\OauthToken;
use App\Models\User;
use App\Models\AppKey;
use App\Models\AppUser;


class OAuthController extends Controller
{

    //Make request for request token
    public static function requestToken(Request $request)
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

        
        if($request->id != null)
        {
            self::saveOauthToken($request->id,$oauth_token,$oauth_token_secret); //Save Temporary Token
        }
        
      
        return redirect('authorize/'.$oauth_token);
      

        //return ["Error"=>$msg,"Response"=>$stream];
        // $msg = 'Successful!';

       // return view('/DiscogsOauth',['next_step'=>'Authorize','message'=>$msg]);

    }


    //Redirect to outside domain(Discogs) to get the authorization from Linnworks
    public static function oauthAuthorize(Request $request)
    {
        //$app = OauthToken::where('app_user_id', $app_user_id)->first();
       
        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $request->oauth_token; 

        return redirect()->away($dir);
    }

    public static function getVerifier(Request $request)
    {  
        $oauth_verifier=$request->oauth_verifier;  //Verifier Authorized by User
        $oauth_token=$request->oauth_token;

        $app=OauthToken::where('oauth_token', $oauth_token)->update([
            'oauth_verifier'=>$oauth_verifier
        ]); 

        $row = OauthToken::firstWhere('oauth_token', $oauth_token);

        self::accessToken($row->app_user_id);
    }

    public static function accessToken($app_user_id)
    {       

        //$res = SendRequest::httpRequest('GET','oauth/access_token',false,'',$oauth_token,$oauth_token_secret,$oauth_verifier);
        $res = SendRequest::httpRequest('GET','oauth/access_token',false,'',$app_user_id);
        
        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Response"=>$stream];
            //return null;
        }

        $stream = Psr7\Query::parse($res['Response']->getBody()->getContents());
        $oauth_token = $stream['oauth_token']; //Permanent Token
        $oauth_token_secret = $stream['oauth_token_secret']; //Permanent Token Secret

        self::saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret);
    }

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

        return view('home1');
    }

    
    public static function DiscogsOauth($app_user_id)
    {
        //echo("DiscogsOauth"."\n");
        return redirect('request_token/'.$app_user_id);
        
    }
    
    //public static function getUsername($token,$token_secret)
    public static function getUsername($app_user_id)
    {
        $dir = 'oauth/identity';

        /*
        $record = OauthToken::first();

        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        //$res = SendRequest::httpGet($dir,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir,true,'',$app_user_id);

        $error=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Username"=>null];
        }

        $stream=json_decode($res['Response']->getBody()->getContents());

        return ["Error"=>$error,"Username"=>$stream->username];
        //return $stream->username;

    }

    //public static function getIdentity($token,$token_secret)
    public static function getIdentity($app_user_id)
    {
        $dir = 'oauth/identity';

        //$res = SendRequest::httpGet($dir,true,$token,$token_secret);
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

    public static function saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret,$oauth_verifier='')
    {
        $record = OauthToken::firstWhere('app_user_id',$app_user_id);

        $app_user = AppUser::firstWhere('id',$app_user_id);
        
        if($record != null)
        {
            $app=$app_user->OauthToken()->update([
                'oauth_token'=>$oauth_token,
                'oauth_secret'=>$oauth_token_secret,
                'oauth_verifier'=>$oauth_verifier
            ]);
        }
        else
        {
            $app=$app_user->OauthToken()->create([
                'oauth_token'=>$oauth_token,
                'oauth_secret'=>$oauth_token_secret,
                'oauth_verifier'=>$oauth_verifier
            ]);
        }

    }
    
    
    
    
    
}
