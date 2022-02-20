<?php

namespace App\Http\Controllers\discogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use App\Models\OauthToken;

class OrderController extends Controller
{
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

            'token' => Config::get('discogsAuth.TOKEN'),
            'token_secret' => Config::get('discogsAuth.TOKEN_SECRET')
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

    //
    public function getOrder($id)
    {
        //$id=$request->
        //echo('Order ID: '.$id.'<br>');

        $dir = 'marketplace/orders/';
        $data = $this->makingRequest($dir.$id);
        $decoded_data=json_decode($data);

        //echo('Data: '.$data.'<br>');

        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');
        
    }
}
