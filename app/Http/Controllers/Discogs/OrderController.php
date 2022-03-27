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
//use App\Models\Linnworks\OrderDespatch as OrderDespatch;

class OrderController extends Controller
{
    //
    public function getOrderById($id,$app_user_id)
    {
        $dir = 'marketplace/orders/';

        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        //$res = SendRequest::httpGet($dir.$id,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir.$id,true,'',$app_user_id);

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
    public static function listOrders($filter='',$PageNumber,$app_user_id)
    {
        $dir = 'marketplace/orders?';

        //$filter='status=New Order';

        //$symbol=$filter=""?"?":"&";

        $p="page=".$PageNumber;
        
        $q=$filter=""?"?".$p:$filter."&".$p;

        /*
        $token = Config::get('discogsAuth.TOKEN'); //Permanent Token
        $token_secret = Config::get('discogsAuth.TOKEN_SECRET'); //Permanent Token Secret
        */

        //$res = SendRequest::httpGet($dir.$q,true,$token,$token_secret);
        $res = SendRequest::httpRequest('GET',$dir.$q,true,'',$app_user_id);

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
        return ["Error"=>$error,"Orders"=>$stream];
    }

    public static function updateOrder($order,$app_user_id) //order is OrderDespatch type
    {
        $error = null;
        $dir = 'marketplace/orders/';
  
        $id=$order->ReferenceNumber;
        
        $msg = 'Shipping Vendor: '.$order->ShippingVendor.' Tracking Number: '.$order->TrackingNumber.' ';
        //$msg = ['Tracking Number'=>$order['TrackingNumber']];
        
        //array_push($q,"'message'=>$msg");
        $q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];

        //$res = SendRequest::httpPost($dir.$id.'/messages',true,$q,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$id.'/messages',true,$q,$app_user_id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"ReferenceNumber"=>$id];
    }
    
}
