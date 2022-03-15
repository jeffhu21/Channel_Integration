<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\ProductController as DiscogsProductController;
use App\Http\Controllers\Discogs\OAuthController as OAuthController;
use Illuminate\Http\Request;

use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
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
        $result = UserInfoAccess::getUserByToken($request);
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

        //dd($postSaleOption->postsale);

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
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        //$user = $result['User'];

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        $row = OAuthController::getIdentity($token,$token_secret);
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
            //echo($attribute_value."<br>");
            //dd('End');

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
                //echo(json_encode($obj->listing)."<br>");
                $res = DiscogsProductController::createList($obj->listing,$token,$token_secret,$token_verifier);
                
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
                $res = DiscogsProductController::updateListing($obj->listing,$token,$token_secret,$token_verifier);
            
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
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        foreach ($request_ids as $data) 
        {

            $res = DiscogsProductController::deleteListing($data,$token,$token_secret,$token_verifier);
            //dd($res);
            //return ['ChannelFeedId'=>$data->ExternalListingId,"Error"=>null];
            return SendResponse::httpResponse(['ChannelFeedId'=>$data->ExternalListingId,"Error"=>null]);
        }

    }

    

    /*
    public function listingUpdate1(Request $request) //ProductsListingsRequest
    {

        $request_listings=json_decode($request->Listings);
        $request_settings=json_decode($request->Settings);

        if($request->Listings == null || count($request_listings) == 0)
        {
            return ['Error' => "Listings Not Found"];
        }
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        //$user = $result['User'];

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        $row = OAuthController::getIdentity($token,$token_secret);
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
            //echo($attribute_value."<br>");
            //dd('End');

            $obj = new Listing();

            $obj->listing1 = [
                'status'=>'Draft',
                'price'=>[
                //"currency"=>'',
                "value"=>intval($listing->Price)
            ],
            'original_price'=>[
                //"curr_abbr"=>'',
                "curr_id"=>0,
                //"formatted"=>'',
                "value"=>intval($listing->Price)
            ],
            "allow_offers"=> false,
            "sleeve_condition"=> $obj->condition['M'],
            //"id"=>'',
            "external_id"=>intval($listing->ExternalListingId),
            //"location"=>'',
            "weight"=>0,
            "format_quantity"=>intval($listing->Quantity),
            "condition"=>$obj->condition['M'],
            "posted"=>now(),
            "ships_from"=>$shipping_from,
            //"uri"=>'',
            "comments"=>$attribute_value,
            'seller'=>[
                "username"=>$row['Stream']->username,
                //"avatar_url"=>'',
                "resource_url"=>$row['Stream']->resource_url,
                //"url"=>'',
                "id"=>intval($row['Stream']->id),
                "shipping"=>$shipping,
                "payment"=>$payment,
                "stats"=>[
                    //"rating"=>'',
                    "stars"=>0,
                    "total"=>0
                ]
            ],
            "shipping_price"=>[
                //"currency"=>'',
                "value"=>0
            ],
            "original_shipping_price"=>[
                //"curr_abbr"=>'',
                "curr_id"=>0,
                //"formatted"=>'',
                "value"=>0
            ],
            "release"=>[
                
                "catalog_number"=>'',
                "resource_url"=>'',
                "year"=>'',
                
                "id"=>intval($listing->SKU),
                "description"=>$listing->Description,
                //"thumbnail"=>''
            ],
            //"resource_url"=>'',
            "audio"=> false
            ];

            if($request->Type == 'CREATE')
            {
                //dd("Calvin");
                dd(json_encode($obj->listing)."<br>");
                DiscogsProductController::createList($obj->listing,$token,$token_secret,$token_verifier);
                //$res = DiscogsProductController::createList($obj->listing,$token,$token_secret);
            }

            if($request->Type == 'UPDATE')
            {
                dd('Yuri');
            }

        }
        
        
    }
    */
}
