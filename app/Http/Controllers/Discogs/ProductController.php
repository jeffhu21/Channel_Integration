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

use App\Models\OauthToken as OauthToken;

class ProductController extends Controller
{
    public static function getInventory($PageNumber,$token,$token_secret)
    {
        /*
        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        */

        $username = null;
        $result = OAuthController::getUsername($token,$token_secret);
        //$username = OAuthController::getUsername()['Username'];

        if($result['Username'] == null)
        {
            return ["Error"=>$result['Error']];
        }

        $username = $result['Username'];
        $q="?page=".$PageNumber;
        $dir = 'users/'.$username.'/inventory'.$q;
        //$dir = 'users/'.$username.'/inventory';

        $res = SendRequest::httpGet($dir,true,$token,$token_secret);

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

    public static function updateInventory($product,$token,$token_secret,$token_verifier)
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

        $res = SendRequest::httpPost($dir.$listing_id,true,$q,$token,$token_secret,$token_verifier);
        
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];

    }

    public static function updatePrice($product,$token,$token_secret,$token_verifier)
    {
        $error = null;
        $dir = 'marketplace/listings/';

        $listing_id = $product->Reference;
        $release_id = $product->SKU;

        $q = ['release_id'=>$release_id,'price'=>$product->Price];

        $res = SendRequest::httpPost($dir.$listing_id,true,$q,$token,$token_secret,$token_verifier);
        
        if($res['Error'] != null)
        {
            $error = $res['Error'];
        }

        return ["Error"=>$error,"SKU"=>$release_id];
    }


}
