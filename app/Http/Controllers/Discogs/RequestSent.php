<?php

namespace App\Http\Controllers\discogs;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

//use App\Models\Linnworks\UserInfo as UserInfo;

class RequestSent
{
    //Set the oauth content header
    public static function contentHeader($authenticated=false)
    {
        $header = [
            'content_type' => $authenticated?'application/json':'application/x-www-form-urlencoded',
            'user_agent' => Config::get('discogsAuth.USER_AGENT'),
        ];
//dd($header);
        return $header;
    }

    //Set the oauth authentication header
    public static function oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier)
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
    public static function makingRequest($dir,$authenticated=false,$oauth_token='',$oauth_token_secret='',$oauth_verifier='')
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        $middleware = self::oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $res = $client->request('GET',$dir,['auth' => 'oauth','header' => 
                    self::contentHeader($authenticated)]);

        if($authenticated == true)
        {
            $stream=json_decode($res->getBody()->getContents());
        }
        else
        {
            $stream = Psr7\Query::parse($res->getBody()->getContents());
        }
        
        //dd(json_encode($stream));

        return $stream;        

    }


    /*
    //OAuthController

    //Set the oauth content header
    public static function contentHeader()
    {
        $header = [
            'content_type' => 'application/x-www-form-urlencoded',
            'user_agent' => Config::get('discogsAuth.USER_AGENT'),
        ];

        return $header;
    }

    //Set the oauth authentication header
    public static function oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier)
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
    public static function makingRequest($dir,$oauth_token='',$oauth_token_secret='',$oauth_verifier='')
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        $middleware = self::oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $res = $client->request('GET',$dir,['auth' => 'oauth','header' => 
                    self::contentHeader()]);

        return $res;        

    }
    */

    /*
    //OrderController

    //Set the oauth content header
    public function contentHeader()
    {
        $header = [
            'content_type' => 'application/json',
            'user_agent' => Config::get('discogsAuth.USER_AGENT'),
        ];

        return $header;
    }

    //Set the oauth authentication header
    public function oauthHeader()
    {
        $middleware = new Oauth1([

            'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            //'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),
            'callback'=>"https://myexample.com",

            'token' => Config::get('discogsAuth.TOKEN'), //Permanent Token
            'token_secret' => Config::get('discogsAuth.TOKEN_SECRET') //Permanent Token Secret
        ]);

        return $middleware;

    }

    //Making request to outside domain Discogs
    public function makingRequest($dir)
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        $middleware = $this->oauthHeader();

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $data = $client->request('GET',$dir,['auth' => 'oauth','header' => 
                    $this->contentHeader()])->getBody()->getContents();

        return $data;

    }
    */

}