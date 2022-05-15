<?php

namespace App\Http\Controllers\Discogs;

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
use Illuminate\Support\Facades\DB;

//use App\Models\OauthToken as OauthToken;
//use App\Models\Linnworks\OrderDespatch as OrderDespatch;
use App\Models\Linnworks\ProductInventory as Product;
use App\Http\Controllers\Discogs\ProductController as ProductController;
use App\Http\Controllers\Discogs\SendRequest as SendRequest;

use App\Models\NotifyFailedDespatchedOrder as NotifyFailedDespatchedOrder;
use App\Models\NotifyFailedDespatchedItem as NotifyFailedDespatchedItem;
use App\Models\Linnworks\OrderDespatch as OrderDespatch;
use App\Models\Linnworks\ItemDespatch as ItemDespatch;

class OrderController extends Controller
{

    /**
         * Scan failed notified orders to Discogs and resend
    */
    public static function autoNotify()
    {
       // $despatchedOrders = [];
        $orders = NotifyFailedDespatchedOrder::all();
        //$despatchedOrders = DB::table('notify_failed_despatched_orders')->join('notify_failed_despatched_items','notify_failed_despatched_orders.ReferenceNumber','=','notify_failed_despatched_items.ReferenceNumber')->get();

        echo(now());

        foreach ($orders as $order) 
        {
            $despatchedOrder = new OrderDespatch();

            $despatchedOrder->ReferenceNumber=$order->ReferenceNumber;
            $despatchedOrder->ShippingVendor=$order->ShippingVendor;
            $despatchedOrder->ShippingMethod=$order->ShippingMethod;
            $despatchedOrder->TrackingNumber=$order->TrackingNumber;
            //$despatchedOrder->SecondaryTrackingNumbers=$order->SecondaryTrackingNumbers;
            $despatchedOrder->ProcessedOn=$order->ProcessedOn;
            $items = NotifyFailedDespatchedItem::where('ReferenceNumber','=',$order->ReferenceNumber)->get();
            
            foreach ($items as $item) 
            {
                $oItem = new ItemDespatch();
                $oItem->SKU=$item->SKU;
                $oItem->OrderLineNumber=$item->OrderLineNumber;
                $oItem->DespatchedQuantity=$item->DespatchedQuantity;
                
                array_push($despatchedOrder->Items,$oItem);
            }
            
            self::updateOrder($despatchedOrder,$order->app_user_id);
        }

        //$despatchedOrders = NotifyFailedDespatchedOrder::all();

        //$despatchedItems = NotifyFailedDespatchedOrder::join('notify_failed_despatched_items');
    }

    /**
         * Retrieve Release Title from Discogs by Release ID
         * @param $id - release id
         * @return [String: $error,HttpResponse->Title]
    */

    /**
         * Retrieve Listings by Listing ID
         * @param $id - listing id
         * @return [String: $error,HttpResponse->Listing]
    */
    //public static function getReleaseTitle($id)
    public static function getListingById($id,$app_user_id)
    {
        //$dir = 'releases/'.$id;
        $dir = 'marketplace/listings/'.$id;

        $res = SendRequest::httpRequest('GET',$dir,true,'',$app_user_id);

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

        return ["Error"=>$error,"Listing"=>$stream];
    }

    /**
         * Retrieve Orders from Discogs by ID
         * @param $id - order id
         * @param $app_user_id - App\Models\AppUser id 
         * @return [String: $error,HttpResponse->Orders]
    */
    public static function getOrderById($id,$app_user_id)
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

        return ["Error"=>$error,"Orders"=>$stream];
    }

    /**
         * Retrieve Orders from Discogs
         * @param $filter - optional filter
         * @param $PageNumber - Page number of the request
         * @param $app_user_id - App\Models\AppUser id 
         * @return [String: $error,HttpResponse->Orders]
    */
    public static function listOrders($PageNumber,$app_user_id,$filter='')
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
  
        $id=$order->ReferenceNumber; //Order ID

        //Update Listing's quantity by minus despatched quantity
        foreach ($order->Items as $item) 
        {
            $product = new Product();
            $product->SKU = $item->SKU;
            $product->Reference = $item->OrderLineNumber;

            $res = self::getListingById($product->Reference,$app_user_id);

            if($res['Error'] != null)
            {
                $error = $res['Error'];
            }
            else
            {
                $qty = $res['Listing']->quantity;
                //dd($qty);
            }

            $product->Quantity = $qty-$item->DespatchedQuantity;

            $updateInventory = ProductController::updateInventory($product,$app_user_id);
        }

        /*
        $q = ['status'=>'Shipped'];
        $res = SendRequest::httpRequest('POST',$dir.$id,true,$q,$app_user_id);
        */


        //Update the message
        $msg = 'Your order is delivered on '.$order->ProcessedOn.', Shipping Method: '.$order->ShippingMethod.', Shipping Vendor: '.$order->ShippingVendor.', Tracking Number: '.$order->TrackingNumber.' '; //To be modified upon the requirement
        
        //$q = ['order_id'=>$id,'status'=>'Payment Received','message'=>$msg,'tracking'=>$msg];
        $q = ['order_id'=>$id,'status'=>'Shipped','message'=>$msg,'tracking'=>$msg];

        $res = SendRequest::httpRequest('POST',$dir.$id.'/messages',true,$q,$app_user_id);
        

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        //Delete the failed order in database if successfully notified
        NotifyFailedDespatchedOrder::where('ReferenceNumber',$id)->delete();

        return ["Error"=>$error,"ReferenceNumber"=>$id];
    }
    
}
