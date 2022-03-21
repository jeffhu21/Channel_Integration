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

use App\Http\Controllers\Discogs\SendRequest as SendRequest;

use App\Models\OauthToken;
use App\Models\User;
use App\Models\AppKey;
//use App\Models\DiscogsApplication;
//use App\Models\LinnworksApplication;

/*
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
*/

class OAuthController extends Controller
{

    //Make request for request token
    public static function requestToken($app_user_id)
    {
        $dir = 'oauth/request_token';
        
        //Get Consumer Key
        $app_keys = AppKey::first();
        
        //Add Owner ID and App User Id in Oauth Token Table
        $app=OauthToken::create([
            'app_user_id'=>$app_user_id,
            'app_owner_id'=>$app_keys->user_id
        ]);

        $res = SendRequest::httpRequest('GET',$dir,false,'',$app_user_id);

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

        $this->saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret); //Save Temporary Token

       // $msg = 'Successful!';

       // return view('/DiscogsOauth',['next_step'=>'Authorize','message'=>$msg]);

    }


    //Redirect to outside domain(Discogs) to get the authorization from Linnworks
    public static function oauthAuthorize($app_user_id)
    {
        $app = OauthToken::where('app_user_id', $app_user_id)->first();

        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $app->oauth_token; 
        
        return redirect()->away($dir);
    }

    public static function getVerifier(Request $request)
    {
        dd($request);
        $oauth_verifier=$request->oauth_verifier;  //Verifier Authorized by User
        $app=OauthToken::where('app_user_id', $app_user_id)->update([
            'oauth_verifier'=>$oauth_verifier
        ]); 
        //$this->saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret);
    }

    public static function accessToken($app_user_id)
    {       
        
        
        //$app = OauthToken::where('app_user_id', $app_user_id)->first();

        /*
        $oauth_token=$request->session()->get('oauth_token'); //Temporary Token
        $oauth_token_secret=$request->session()->get('oauth_token_secret'); //Temporary Token Secret
        */

        //$oauth_token=$app->oauth_token; //Temporary Token
        //$oauth_token_secret=$app->oauth_secret; //Temporary Token Secret

        //$oauth_verifier=$oauth_verifier;  //Verifier Authorized by User

        //$res = SendRequest::httpGet('oauth/access_token',false,$oauth_token,$oauth_token_secret,$oauth_verifier);

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
        

        $this->saveOauthToken($oauth_token,$oauth_token_secret);

        //$this->saveOauthToken2($oauth_token,$oauth_token_secret,$oauth_verifier);

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
        self::requestToken($app_user_id);
        
        self::oauthAuthorize($app_user_id);

        
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

    public function saveOauthToken($app_user_id,$oauth_token,$oauth_token_secret,$oauth_verifier='')
    {
        $app=OauthToken::where('app_user_id', $app_user_id)->update([
            'oauth_token'=>$oauth_token,
            'oauth_secret'=>$oauth_token_secret,
            'oauth_verifier'=>$oauth_verifier
        ]);
    }
    
    
    
    
    
}
