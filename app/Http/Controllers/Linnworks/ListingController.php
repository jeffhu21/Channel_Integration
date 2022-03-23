<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\ProductController as DiscogsProductController;
use App\Http\Controllers\Discogs\OAuthController as OAuthController;
use Illuminate\Http\Request;

use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\ProductsListings as ProductsListingsRequest;

use App\Models\Linnworks\PostSaleOptions as PostSaleOptions;
use App\Models\OauthToken as OauthToken;
use App\Models\Discogs\Listing as Listing;


class ListingController extends Controller
{
    //
    public function postSaleOptions(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }

        $postSaleOption = new PostSaleOptions();

        $postSaleOption->postsale=[
            "Error"=>null,
            "CanCancel"=> true,
            "CanCancelOrderLines"=> true,
            "CanCancelOrderLinesPartially"=> true,
            "AutomaticRefundOnCancel"=> false,
            "CanRefund"=> true,
            "CanAttachRefundToItem"=> true,
            "CanAttachRefundToService"=> false,
            "RefundShippingTypes"=> $postSaleOption->Type['NotSupported'],
            "CanRefundAdditionally"=> false,
            "CanReturn"=> false
        ];


        return SendResponse::httpResponse($postSaleOption->postsale);
    }

    

    public function listingUpdate(Request $request) //ProductsListingsRequest
    {

        $request_listings=json_decode($request->Listings);
        $request_settings=json_decode($request->Settings);

        if($request->Listings == null || count($request_listings) == 0)
        {
            return ['Error' => "Listings Not Found"];
        }
        
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];

        $row = OAuthController::getIdentity($app_user->id);
        if($row['Error'] != null)
        {
            return ["Error"=>$row['Error']];
        }

        $shipping_from = "";

        if($request->Settings != null && count($request_settings->ShippingSettings) != 0)
        {
            foreach ($request_settings->ShippingSettings as $shipping_setting) 
            {
                if(isset($shipping_setting->Values))
                {
                    $shipping_from = $shipping_setting->Values[0].' '.$shipping_from;
                }
            }      
        }

        $payment = "";

        if($request_settings->PaymentSettings[0] != null && count($request_settings->PaymentSettings) != 0)
        {
            foreach ($request_settings->PaymentSettings as $payment_setting) 
            {
                if(isset($payment_setting->Values))
                {
                    $payment = $payment_setting->Values[0].' '.$payment;
                }
            }    
        }

        foreach ($request_listings as $listing) 
        {
            $attribute_value = "";
            $shipping = "";

            foreach ($listing->ShippingMethods as $shipping_method) 
            {
                if(isset($shipping_method->Values))
                {
                    $shipping = $shipping.$shipping_method->Price.', '.$shipping_method->ShippingMethodID.'';
                }
            }      

            foreach ($listing->Attributes as $attribute) 
            {
                $attribute_value = $attribute_value.' '.$attribute->AttributeValue;
            }    

            $obj = new Listing();

            $obj->listing = [
                'listing_id'=>$request->Type == 'CREATE'?0:$listing->ExternalListingId,
                'release_id'=>intval($listing->SKU),
                //'release_id'=>5684943,
                //'release_id'=>111,
                'condition'=>$obj->condition['M'],
                'sleeve_condition'=>$obj->condition['M'],
                'price'=>intval($listing->Price),
                'comments'=>$attribute_value,
                'allow_offers'=>false,
                'status'=>'Draft',
                'external_id'=>'',
                'location'=>'',
                'weight'=>0,
                'format_quantity'=>intval($listing->Quantity)
            ];

            if($request->Type == 'CREATE')
            {
                
                $res = DiscogsProductController::createList($obj->listing,$app_user->id);
                
                //dd(json_decode($res['Response']->getBody()->getContents())->listing_id);

                /*
                if($res['Error'] != null)
                {
                    return ['Error'=>$res['Error']];
                    //$error = $error.$res['Error']."\n";
                    //$UpdateInventory = $UpdateInventory.$res["SKU"].", ";   
                }  
                
                return ['ChannelFeedId'=>json_decode($res['Response']->getBody()->getContents())->listing_id,"Error"=>null];
                */
            }

            if($request->Type == 'UPDATE')
            {
                $res = DiscogsProductController::updateListing($obj->listing,$app_user->id);
            
            }
            if($res['Error'] != null)
            {
                return ['Error'=>$res['Error']];
                //$error = $error.$res['Error']."\n";
                //$UpdateInventory = $UpdateInventory.$res["SKU"].", ";   
            }  
                
            //return ['ChannelFeedId'=>json_decode($res['Response']->getBody()->getContents())->listing_id,"Error"=>null];
            return SendResponse::httpResponse(['ChannelFeedId'=>json_decode($res['Response']->getBody()->getContents())->listing_id,"Error"=>null]);
        }    
    }

    public function listingDelete(Request $request)
    {
        $request_ids=json_decode($request->ExternalListingIds);

        //dd(count($request_ids));

        if($request->ExternalListingIds == null || count($request_ids) == 0)
        {
            return ['Error' => "External Listing Ids Not Found"];
        }
        
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }

        $app_user = $result['User'];

        foreach ($request_ids as $data) 
        {

            $res = DiscogsProductController::deleteListing($data,$app_user->id);
            //dd($res);
            //return ['ChannelFeedId'=>$data->ExternalListingId,"Error"=>null];
            return SendResponse::httpResponse(['ChannelFeedId'=>$data->ExternalListingId,"Error"=>null]);
        }

    }
}
