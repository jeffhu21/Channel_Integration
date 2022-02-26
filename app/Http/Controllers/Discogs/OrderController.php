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
    //
    public function getOrderById($id)
    {

        $dir = 'marketplace/orders/';

        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        
        //$token = 'hArUMyYtnQnUfdgycKkKgFbjfEjjwqhUPgrhWENj'; //Permanent Token
        //$token_secret = 'SWnIgNmPOjqfdqZlIcNvFbCXajZtTRGkmSgIZyfi'; //Permanent Token Secret

        $stream = RequestSent::makingRequest($dir.$id,true,$token,$token_secret);

        //dd($stream);
      
        //echo(json_encode($stream));
        return $stream;
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');
    }

    public static function listOrders($filter='')
    {
        $dir = 'marketplace/orders?';

        //$filter='status=New Order';

        //dd($filter);

        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret

        $stream = RequestSent::makingRequest($dir.$filter,true,$token,$token_secret);

        
        //echo('Data: <br>');
        return $stream;
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');

    }
}
