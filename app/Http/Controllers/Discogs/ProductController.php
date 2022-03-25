<?php

namespace App\Http\Controllers\Discogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use App\Http\Controllers\Discogs\OAuthController as OAuthController;
use App\Http\Controllers\Discogs\SendRequest as SendRequest;

//use App\Models\OauthToken as OauthToken;
use App\Models\Discogs\Listing as Listing;

class ProductController extends Controller
{
    //public static function getInventory($PageNumber,$token,$token_secret)
    public static function getInventory($PageNumber,$app_user_id)
    {
        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        $username = null;
        //$result = OAuthController::getIdentity($token,$token_secret);
        $result = OAuthController::getIdentity($app_user_id);
        //$username = OAuthController::getUsername()['Username'];

        if($result['Error'] != null)
        {
            return ["Error"=>$result['Error']];
        }

        $username = $result['Stream']->username;
        $q="?page=".$PageNumber;
        $dir = 'users/'.$username.'/inventory'.$q;
        //$dir = 'users/'.$username.'/inventory';

        //$res = SendRequest::httpGet($dir,true,$token,$token_secret);
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

        //dd($stream);
        return ["Error"=>$error,"Products"=>$stream];
    }

    public static function updateInventory($product,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';

        $listing_id = $product->Reference;
        $release_id = $product->SKU;

        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;
        */

        $q = ['release_id'=>$release_id,'format_quantity'=>$product->Quantity];

        //$res = SendRequest::httpPost($dir.$listing_id,true,$q,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$q,$app_user_id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];

    }

    public static function updatePrice($product,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';

        $listing_id = $product->Reference;
        $release_id = $product->SKU;

        $q = ['release_id'=>$release_id,'price'=>$product->Price];

        //$res = SendRequest::httpPost($dir.$listing_id,true,$q,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$q,$app_user_id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];
    }

    public static function createList($listing,$app_user_id)
    {
        $error = null;
        $dir ='marketplace/listings';

        //$q = ['release_id'=>$release_id,'price'=>$product->Price];

        //dd($listing['status']);

        //$res = SendRequest::httpPost($dir,true,$listing,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir,true,$listing,$app_user_id);

        //dd($res['Response']->getBody()->getContents());

        /*
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }
        */

        return $res;
        //return ['listing_id'=>$listing];
    }

    public static function updateListing($listing,$app_user_id)
    {
        $error = null;
        $dir = 'marketplace/listings/';
        $listing_id = $listing['listing_id'];

        //$res = SendRequest::httpPost($dir.$listing_id,true,$listing,$token,$token_secret,$token_verifier);
        $res = SendRequest::httpRequest('POST',$dir.$listing_id,true,$listing,$app_user_id);

        return $res;
    }
    
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
