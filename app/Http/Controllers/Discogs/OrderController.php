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

        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        //$res = SendRequest::httpGet($dir.$id,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir.$id,true,'',$token,$token_secret);

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
        return ["Error"=>$error,"Order"=>$stream];
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');
    }

    //Retrieve Orders from Discogs
    public static function listOrders($filter='',$PageNumber,$token,$token_secret)
    {
        $dir = 'marketplace/orders?';

        //$filter='status=New Order';

        //dd($filter);

        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        //$symbol=$filter=""?"?":"&";

        $p="page=".$PageNumber;
        
        $q=$filter=""?"?".$p:$filter."&".$p;

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        //$res = SendRequest::httpGet($dir.$q,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir.$q,true,'',$token,$token_secret);

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
        return ["Error"=>$error,"Orders"=>$stream];
        //echo('Resource URL: '.$decoded_data->resource_url.'<br>');

    }

    public static function updateOrder($order,$token,$token_secret,$token_verifier) //order is OrderDespatch type
    {
        $error = null;
        $dir = 'marketplace/orders/';
  
        $id=$order->ReferenceNumber;
        
        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;
        */
        
        $msg = 'Shipping Vendor: '.$order->ShippingVendor.' Tracking Number: '.$order->TrackingNumber.' ';
        //$msg = ['Tracking Number'=>$order['TrackingNumber']];
        
        //array_push($q,"'message'=>$msg");
        $q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];

        //$res = SendRequest::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$id.'/messages',true,$q,$token,$token_secret);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"ReferenceNumber"=>$id];
    }

    public static function updateOrder1(OrderDespatch $obj)
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
        //$res = SendRequest::httpPost($dir.$id.'/',true,$q,$token,$token_secret,$token_verifier);
        
        $msg = 'Shipping Vendor: '.$obj->order->ShippingVendor.' Tracking Number: '.$obj->order->TrackingNumber.' ';
        //$msg = ['Tracking Number'=>$order['TrackingNumber']];
        
        //array_push($q,"'message'=>$msg");
        $q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];

        //$res = SendRequest::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$id.'/messages',true,$q,$token,$token_secret);
        //dd($res);
        /*
        if($res->getStatusCode()!=200)
        {
            $error='Despatch failed for some reason';

            //return ["Error"=>$error,"ReferenceNumber"=>$id];
        }
        else
        {
            $res = SendRequest::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
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
        $testing = SendRequest::testing();
    }
    
}
