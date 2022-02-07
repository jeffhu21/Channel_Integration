<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
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
            'user_agent' => env('USER_AGENT'),
        ];

        return $header;
    }

    //Set the oauth authentication header
    public function oauthHeader($oauth_token='',$oauth_token_secret='')
    {
        $middleware = new Oauth1([

            'consumer_key' => env('CONSUMER_KEY'),
            'consumer_secret' => env('CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>"2225112819",
            'callback'=>"https://www.linnworks.net/#/app/HomePage",

            'token' => $oauth_token,
            'token_secret' => $oauth_token_secret,
            
        ]);

        return $middleware;

    }

    //Make request for request token
    public function requestToken()
    {
        $BASE_URL = 'https://api.discogs.com/oauth/';
        $stack = HandlerStack::create(); 

        $middleware = $this->oauthHeader();

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $response = $client->request('GET','request_token',['auth' => 'oauth','header' => $this->contentHeader()]);

        $data=$response->getBody()->getContents();
        $stream = Psr7\Query::parse($data);
        $oauth_token = $stream['oauth_token'];
        $oauth_token_secret = $stream['oauth_token_secret'];

        echo('OAuth Token: '.$oauth_token.' <br>');

        $a_res=$this->oauthAuthorize($oauth_token);

        //dd($a_res);

        //echo('Authorize: '.$a_res.' <br>');
    }

    //Make authorization
    public function oauthAuthorize($oauth_token)
    {
        $dir = 'https://www.discogs.com/oauth/authorize?oauth_token=' . $oauth_token;
        //$base = 'https://www.discogs.com/oauth/';
        //$dir = 'authorize?oauth_token=' . $oauth_token;

        
        
        
         return redirect()->away($dir);
    }

    public function accessToken()
    {
        
    }
    

    public function test()
    {
        $client = new Client();
        $uri='/www.yorku.ca';
        $onRedirect = function(
            RequestInterface $request,
            ResponseInterface $response,
            UriInterface $uri
        ) {
            echo 'Redirecting! ' . $request->getUri() . ' to ' . $uri . "\n";
        };
        
        $res = $client->request('GET', '/www.google.ca', [
            'allow_redirects' => [
                'max'             => 10,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                'protocols'       => ['https'], // only allow https URLs
                'on_redirect'     => $onRedirect,
                'track_redirects' => true
            ]
        ]);
        
        echo $res->getStatusCode();
        // 200
        
        echo $res->getHeaderLine('X-Guzzle-Redirect-History');
        // http://first-redirect, http://second-redirect, etc...
        
        echo $res->getHeaderLine('X-Guzzle-Redirect-Status-History');

        
    }

    public function test1($a='alan')
    {
        
    }

    

    

}
