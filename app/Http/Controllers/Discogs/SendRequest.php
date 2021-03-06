<?php

namespace App\Http\Controllers\Discogs;

//use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
/*
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
*/
use App\Models\OauthToken;
use App\Models\AppKey;

//use App\Models\AppUser as AppUser;

class SendRequest
{
    /**
         * Set the content type of header
         * @param $authenticated - optional
         * @return $header - request header
    */
    public static function contentHeader($authenticated=false)
    {
        $header = [
            'content_type' => $authenticated?'application/json':'application/x-www-form-urlencoded',
            'user_agent' => 'linnworksDiscogs/0.1 +https://www.example.com'
            //'user_agent' => Config::get('discogsAuth.USER_AGENT')

        ];
        return $header;
    }

    /**
         * Set the oauth header
         * @param $app_user_id - App\Models\AppUser id
         * @return $middleware - oauth header
    */
    public static function oauthHeader($app_user_id)
    {

        //Get Consumer Key
        $app_keys = AppKey::first();

        if($app_keys == null)
        {
            return null;
        }

        $app = null;

        if($app_user_id != 0)
        {
            //Retrieve Oauth Token
            $app = OauthToken::firstWhere('app_user_id',$app_user_id);
            
        }

        $middleware = new Oauth1([

            'consumer_key' => $app_keys->discogs_consumer_key,
            'consumer_secret' => $app_keys->discogs_consumer_secret,

           //'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            //'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            //'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),

            'callback'=>$app_keys->callback_url,

            'token' => ($app_user_id!=0)?$app->oauth_token:'',
            'token_secret' => ($app_user_id!=0)?$app->oauth_secret:'',
            'verifier' => ($app_user_id!=0)?$app->oauth_verifier:''
        ]);
        return $middleware;

    }

    /**
         * Send the http request to url of Discogs
         * @param $method - GET,POST or DELETE method
         * @param $dir - part of url
         * @param $authenticated - optional
         * @param $q - optional query
         * @param $app_user_id - optional 
         * @return [String:$error,HttpResponse:$res]
    */
    public static function httpRequest($method,$dir,$authenticated=false,$q='',$app_user_id=0)
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 
        $error = null;
        $res = null;

        $middleware = self::oauthHeader($app_user_id);

        if($middleware != null)
        {
            $stack->push($middleware);
        }

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);
        
        try
        {
            
            switch ($method) 
            {
                case 'GET':
                    $res = $client->request('GET',$dir,['auth' => 'oauth','header' => self::contentHeader($authenticated)]);
                    break;
                case 'POST':
                    $res = $client->request('POST',$dir,['auth' => 'oauth','header' => self::contentHeader($authenticated),'json'=>$q]);
                    break;
                case 'DELETE':
                    $res = $client->request('DELETE',$dir,['auth' => 'oauth','header' => self::contentHeader($authenticated)]);
                    break;
                
            }
            if($res->getStatusCode()!=200 && $res->getStatusCode()!=201 && $res->getStatusCode()!=204)
            {
                $error=$res->getStatusCode()." and " .$res->getReasonPhrase();
            }
        
        }
        catch(RequestException $ex)
        {
            $error = $ex->getMessage();
        }
        catch(ServerException $ex)
        {
            $error = $ex->getMessage();
        }
        catch(ClientException $ex)
        {
            $error = $ex->getMessage();
        }
        
        finally
        {
            if($res == null)
            {        
                $error = 'Something Wrong'; 
            }
        }
        
        return ["Error"=>$error,"Response"=>$res];
    }
}