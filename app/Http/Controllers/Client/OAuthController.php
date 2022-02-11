<?php

namespace App\Http\Controllers\client;

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

use App\Models\OauthToken;

/*
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
*/

class OAuthController extends Controller
{
    //public $oauth_token;

    //Set the oauth content header
    public function contentHeader()
    {
        $header = [
            'content_type' => 'application/x-www-form-urlencoded',
            'user_agent' => Config::get('discogsAuth.USER_AGENT'),
        ];

        return $header;
    }

    //Set the oauth authentication header
    public function oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier)
    {
        $middleware = new Oauth1([

            'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            //'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),
            'callback'=>"https://localhost:8080",

            'token' => $oauth_token,
            'token_secret' => $oauth_token_secret,
            'verifier' => $oauth_verifier
        ]);

        return $middleware;

    }

    //Making request to outside domain Discogs
    public function makingRequest($dir,$oauth_token='',$oauth_token_secret='',$oauth_verifier='')
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        $middleware = $this->oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $res = $client->request('GET',$dir,['auth' => 'oauth','header' => 
                    $this->contentHeader()]);

        return $res;        

    }

    //Make request for request token
    public function requestToken()
    {
        $res = $this->makingRequest('oauth/request_token');

        //$data=$response;
        $stream = Psr7\Query::parse($res->getBody()->getContents());
        $oauth_token = $stream['oauth_token'];
        $oauth_token_secret = $stream['oauth_token_secret'];

        /*
        echo('OAuth Token: '.$oauth_token.' <br>');
        echo('OAuth Token Secret: '.$oauth_token_secret.' <br>');
        */

        session(['oauth_token'=>$oauth_token]);
        session(['oauth_token_secret'=>$oauth_token_secret]);

        if($res->getStatusCode()==200)
        {
            $msg = 'Successful!';
        }
        else
        {
            $msg = 'Error!';
        }

        return view('/home',['response'=>$msg]);

        //$a_res=$this->oauthAuthorize($oauth_token);

        //echo('Authorize: '.$a_res.' <br>');

        //dd($a_res);
        //return view('home',['response' => $a_res]);

    }

    //Redirect to outside domain(Discogs) to get the authorization from Linnworks
    public function oauthAuthorize(Request $request)
    {
        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $request->session()->get('oauth_token'); 
        
        return redirect()->away($dir);
    }

    public function accessToken(Request $request)
    {        
        $oauth_token=$request->session()->get('oauth_token');
        $oauth_token_secret=$request->session()->get('oauth_token_secret');
        $oauth_verifier=$request->oauth_verifier;

        echo($oauth_verifier.'<br>');

        $res = $this->makingRequest('oauth/access_token',$oauth_token,$oauth_token_secret,$oauth_verifier);
        
        $stream = Psr7\Query::parse($res->getBody()->getContents());
        $oauth_token = $stream['oauth_token'];
        $oauth_token_secret = $stream['oauth_token_secret'];


        //echo('OAuth Token: '.$oauth_token.' <br>');
        //echo('OAuth Token Secret: '.$oauth_token_secret.' <br>');
        $this->saveToken($oauth_token,$oauth_token_secret);

        //shwSWYSutjiYqQChwPxIYeibaTDIfPzosUyUpEws 
    }

    /*
    public function saveToken($oauth_token,$oauth_token_secret)
    {
        $token=OauthToken::create([
            'consumer_key'=> env('CONSUMER_KEY'),
            'consumer_secret'=> env('CONSUMER_SECRET'),
            'oauth_token'=>$oauth_token,
            'oauth_secret'=>$oauth_token_secret
        ]);
    }
    */

    public function getOrder($id)
    {
        //echo Config::get('discogsAuth.CONSUMER_KEY');

    }
}
