<?php

namespace App\Http\Controllers\linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    
    //Set the auth content header
    public function contentHeader()
    {
        $header = [
            'content_type' => 'application/x-www-form-urlencoded',
            'connection' => 'keep-alive',
            'accept'=> 'application/json',
            'accept_encoding'=> 'gzip, deflate',
            
        ];

        return $header;
    }

    //Set the auth authentication header
    public function authBody()
    {

        $body = [

        ];

        return $body;
        /*
        $middleware = new Oauth1([

            'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),
            'callback'=>"https://localhost:8080",

            'token' => $oauth_token,
            'token_secret' => $oauth_token_secret,
            'verifier' => $oauth_verifier
        ]);

        return $middleware;
        */
    }

    public function postBack($token)
    {
        echo ($token);
        return 'Post Callback';
    }

    public function token($token)
    {
        echo ($token);
        return 'Auth Token';
    }

}
