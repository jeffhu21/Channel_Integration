<?php

namespace App\Http\Controllers\discogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
/*
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
*/
//use App\Models\OauthToken as OauthToken;
//use App\Models\Linnworks\OrderDespatch as OrderDespatch;
use App\Http\Controllers\Discogs\SendRequest as SendRequest;

class OrderController extends Controller
{
    /**
         * Retrieve Orders from Discogs by ID
         * @param $id - order id
         * @param $app_user_id - App\Models\AppUser id 
         * @return [String: $error,HttpResponse->Orders]
    */
    public function getOrderById($id,$app_user_id)
    {
        $dir = 'marketplace/orders/';

        $res = SendRequest::httpRequest('GET',$dir.$id,true,'',$app_user_id);

        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }
        else
        {
            $stream=json_decode($res['Response']->getBody()->getContents());
        }

        return ["Error"=>$error,"Order"=>$stream];
    }

    /**
         * Retrieve Orders from Discogs
         * @param $filter - optional filter
         * @param $PageNumber - Page number of the request
         * @param $app_user_id - App\Models\AppUser id 
         * @return [String: $error,HttpResponse->Orders]
    */
    public static function listOrders($filter='',$PageNumber,$app_user_id)
    {
        $dir = 'marketplace/orders?';

        $p="page=".$PageNumber;
        $q=$filter=""?"?".$p:$filter."&".$p;
        $res = SendRequest::httpRequest('GET',$dir.$q,true,'',$app_user_id);

        $error=null;
        $stream=null;
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }
        else
        {
            $stream=json_decode($res['Response']->getBody()->getContents());
        }
        return ["Error"=>$error,"Orders"=>$stream];
    }

    //To be modified
    /**
     * Update Despatched Orders in Discogs by sending request
     * @param $order - despatched order
     * @param $app_user_id - App\Models\AppUser id 
     * @return [String:$error,String:ReferenceNumber]
     */
    public static function updateOrder($order,$app_user_id) //order is OrderDespatch type
    {
        $error = null;
        $dir = 'marketplace/orders/';
  
        $id=$order['ReferenceNumber']; //Order ID
        
        //TO DO: Update the status of the despatched order from New Order to Shipped

        /*
        $q = ['status'=>'Shipped'];
        $res = SendRequest::httpRequest('POST',$dir.$id,true,$q,$app_user_id);
        */


        //Update the message
        $msg = 'Your order is on its way, Shipping Vendor: '.$order['ShippingVendor'].' Tracking Number: '.$order['TrackingNumber'].' '; //To be modified upon the requirement
        
        //$q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];
        $q = ['order_id'=>$id,'status'=>'Shipped','message'=>$msg,'tracking'=>$msg];

        $res = SendRequest::httpRequest('POST',$dir.$id.'/messages',true,$q,$app_user_id);
        

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"ReferenceNumber"=>$id];
    }
    
}
