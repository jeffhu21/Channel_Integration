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

use App\Models\OauthToken as OauthToken;
use App\Models\Linnworks\OrderDespatch as OrderDespatch;

class OrderController extends Controller
{
    //
    public function getOrderById($id)
    {

        $dir = 'marketplace/orders/';

        $record = OauthToken::first();
        
        //dd($record->oauth_verifier);

        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        $res = RequestSent::httpGet($dir.$id,true,$token,$token_secret);

        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            //return null;
        }
        else
        {
            $stream=json_decode($res['Response']->getBody()->getContents());
        }

        //dd($stream);
      
        //echo(json_encode($stream));
        return ["Error"=>$error,"Response"=>$stream];
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');
    }

    //Retrieve Orders from Discogs
    public static function listOrders($filter='')
    {
        $dir = 'marketplace/orders?';

        //$filter='status=New Order';

        //dd($filter);

        $record = OauthToken::first();

        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        $res = RequestSent::httpGet($dir.$filter,true,$token,$token_secret);

        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
            //return null;
        }
        else
        {
            $stream=json_decode($res['Response']->getBody()->getContents());
        }

        //echo(count(($stream)->orders).'<br>');

        //echo('Data: <br>');
        return ["Error"=>$error,"Response"=>$stream];
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');

    }

    public static function updateOrder(OrderDespatch $obj)
    {
        $error = null;
        $dir = 'marketplace/orders/';
        //dd($obj->order->ReferenceNumber);
        $id=$obj->order->ReferenceNumber;
        
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;
        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */
        //$res = RequestSent::httpPost($dir.$id.'/',true,$q,$token,$token_secret,$token_verifier);
        
        $msg = 'Shipping Vendor: '.$obj->order->ShippingVendor.' Tracking Number: '.$obj->order->TrackingNumber.' ';
        //$msg = ['Tracking Number'=>$order['TrackingNumber']];
        
        //array_push($q,"'message'=>$msg");
        $q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];

        $res = RequestSent::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
        //dd($res);
        /*
        if($res->getStatusCode()!=200)
        {
            $error='Despatch failed for some reason';

            //return ["Error"=>$error,"ReferenceNumber"=>$id];
        }
        else
        {
            $res = RequestSent::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
        }
        */
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }
        else
        {
            //dd($res['Response']);
        }

        return ["Error"=>$error,"ReferenceNumber"=>$id];
    }

    public function testing()
    {
        $testing = RequestSent::testing();
    }
    
}
