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

use App\Http\Controllers\Discogs\OAuthController as OAuthController;
use App\Http\Controllers\Discogs\SendRequest as SendRequest;

//use App\Models\OauthToken as OauthToken;
//use App\Models\Discogs\Listing as Listing;

class ProductController extends Controller
{
    /**
         * Retrieve Inventories from Discogs
         * @param $PageNumber - Page number of the request
         * @param $app_user_id - App\Models\AppUser id 
         * @return [String: $error,HttpResponse->Products]
    */
    public static function getInventory($PageNumber,$app_user_id)
    {
        $username = null;
        $result = OAuthController::getIdentity($app_user_id);

        if($result['Error'] != null)
        {
            return ["Error"=>$result['Error']];
        }

        $username = $result['Stream']->username;
        $q="?page=".$PageNumber;
        $dir = 'users/'.$username.'/inventory'.$q;
        
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

        return ["Error"=>$error,"Products"=>$stream];
    }


    /**
     * Update Inventory of Products in Discogs by sending request
     * @param $product - product from Linnworks
     * @param $app_user_id - App\Models\AppUser id 
     * @return [String:$error,String:SKU]
     */
    public static function updateInventory($product,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';

        $listing_id = $product['Reference'];
        $release_id = $product['SKU'];

        $q = ['release_id'=>$release_id,'format_quantity'=>$product['Quantity']];

        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$q,$app_user_id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];

    }

    /**
     * Update Price of Products in Discogs by sending request
     * @param $product - product from Linnworks
     * @param $app_user_id - App\Models\AppUser id 
     * @return [String:$error,String:SKU]
     */
    public static function updatePrice($product,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';

        $listing_id = $product['Reference'];
        $release_id = $product['SKU'];

        $q = ['release_id'=>$release_id,'price'=>$product['Price']];

        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$q,$app_user_id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];
    }

    /**
     * Create a new list of items in Discogs
     * @param $listing - new listing from Linnworks
     * @param $app_user_id - App\Models\AppUser id 
     * @return "HttpRequest: [String: listing_id]"
     */
    public static function createList($listing,$app_user_id)
    {
        $error = null;
        $dir ='marketplace/listings';

        $res = SendRequest::httpRequest('POST',$dir,true,$listing,$app_user_id);

        return $res;
    }

    /**
     * Update the list of items in Discogs
     * @param $listing - listing from Linnworks
     * @param $app_user_id - App\Models\AppUser id 
     * @return "HttpRequest"
     */
    public static function updateListing($listing,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';
        $listing_id = $listing['listing_id'];

        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$listing,$app_user_id);

        return $res;
    }
    
    /**
     * Delete the list of items in Discogs
     * @param $listing - listing from Linnworks
     * @param $app_user_id - App\Models\AppUser id 
     * @return "HttpRequest"
     */
    public static function deleteListing($listing,$app_user_id)
    {
        $res = null;
        $error = null;
        $dir = 'marketplace/listings/';

        //if(isset($listing['listing_id']))
        //{
            $listing_id = $listing->ExternalListingId;
            $res = SendRequest::httpRequest('DELETE',$dir.$listing_id,true,'',$app_user_id);
            
        //}
        //else
        //{
        //    $error = 'Id Not Found';
        //}
        
        return $res;
    }

}
