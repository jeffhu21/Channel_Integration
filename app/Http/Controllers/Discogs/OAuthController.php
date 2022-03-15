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

use App\Http\Controllers\Discogs\SendRequest as SendRequest;

use App\Models\OauthToken;

use App\Models\DiscogsApplication;
use App\Models\LinnworksApplication;

/*
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
*/

class OAuthController extends Controller
{
    //public $oauth_token;

    //Make request for request token
    public function requestToken(Request $request)
    {
        //dd($request);
      
        //$res = SendRequest::httpGet('oauth/request_token');
        $res = SendRequest::httpRequest('GET','oauth/request_token');

        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Response"=>$stream];
            //return null;
        }
        
        $stream = Psr7\Query::parse($res['Response']->getBody()->getContents());
        $oauth_token = $stream['oauth_token']; //Temporary Token
        $oauth_token_secret = $stream['oauth_token_secret']; //Temporary Token Secret

        
       // echo('OAuth Token: '.$oauth_token.' <br>');
        //echo('OAuth Token Secret: '.$oauth_token_secret.' <br>');
        

        session(['oauth_token'=>$oauth_token]); //Save Temporary Token to Session
        session(['oauth_token_secret'=>$oauth_token_secret]); //Save Temporary Token Secret to Session

        /*
        if($res->getStatusCode()==200)
        {
            //dd($oauth_token . ' and ' . $oauth_token_secret.'<br>');
            $msg = 'Successful!';
        }
        else
        {
            $msg = 'Error!';
        }
        */

        return view('/home',['response'=>$error]);

    }

    //Redirect to outside domain(Discogs) to get the authorization from Linnworks
    public function oauthAuthorize(Request $request)
    {
        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $request->session()->get('oauth_token'); 
        
        return redirect()->away($dir);
    }

    public function accessToken(Request $request)
    {        
        $oauth_token=$request->session()->get('oauth_token'); //Temporary Token
        $oauth_token_secret=$request->session()->get('oauth_token_secret'); //Temporary Token Secret
        $oauth_verifier=$request->oauth_verifier;  //Verifier Authorized by User

        echo($oauth_verifier.'<br>');

        //$res = SendRequest::httpGet('oauth/access_token',false,$oauth_token,$oauth_token_secret,$oauth_verifier);
        $res = SendRequest::httpRequest('GET','oauth/access_token',false,'',$oauth_token,$oauth_token_secret,$oauth_verifier);

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
        

        //echo('OAuth Token: '.$oauth_token.' <br>');
        //echo('OAuth Token Secret: '.$oauth_token_secret.' <br>');

        $this->saveToken($oauth_token,$oauth_token_secret,$oauth_verifier);

        //shwSWYSutjiYqQChwPxIYeibaTDIfPzosUyUpEws 
    }

    public static function getUsername($token,$token_secret)
    {
        $dir = 'oauth/identity';

        /*
        $record = OauthToken::first();

        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        //$res = SendRequest::httpGet($dir,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir,true,'',$token,$token_secret);

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

    public static function getIdentity($token,$token_secret)
    {
        $dir = 'oauth/identity';

        //$res = SendRequest::httpGet($dir,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir,true,'',$token,$token_secret);

        $error=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ["Error"=>$error,"Stream"=>null];
        }

        $stream=json_decode($res['Response']->getBody()->getContents());

        return ["Error"=>$error,"Stream"=>$stream];
    }

    
    public function saveToken($oauth_token,$oauth_token_secret,$oauth_verifier)
    {
        $token=OauthToken::create([
            'consumer_key'=> env('CONSUMER_KEY'),
            'consumer_secret'=> env('CONSUMER_SECRET'),
            'oauth_token'=>$oauth_token,
            'oauth_secret'=>$oauth_token_secret,
            'oauth_verifier'=>$oauth_verifier
        ]);
    }
    
    //Added function
    public function saveLinnworksAuthToken(Request $request)
    {
        //var_dump($token);

        if(!$request->has('token'))
        {
            return 'Error';
        }

        $token = $request->token;

        $record=LinnworksApplication::create([
            'application_id'=>'',
            'application_secret'=>'',
            'token'=>$token
        ]);

        //echo('Record: '.$record."\n");
        
        return 'Hello!';
        
    }

    
}
