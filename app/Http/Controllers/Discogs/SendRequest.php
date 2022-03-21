<?php

namespace App\Http\Controllers\discogs;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;

use App\Models\OauthToken;
use App\Models\DiscogsApplication;
use App\Models\AppKey;

//use App\Models\Linnworks\AppUser as AppUser;

class SendRequest
{
    //Set the oauth content header
    public static function contentHeader($authenticated=false)
    {
        $header = [
            'content_type' => $authenticated?'application/json':'application/x-www-form-urlencoded',
            'user_agent' => Config::get('discogsAuth.USER_AGENT')
            //'accept'=>'application/json'
        ];
        //dd($header);
        return $header;
    }

    //Set the oauth authentication header
    //public static function oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier)
    public static function oauthHeader($app_user_id)
    {

        //$app = DiscogsApplication::firstWhere('user_id',Auth::user()->id);
        
       // $app = AppKey->first();

        //$app = OauthToken::firstWhere('app_user_id',$app_user_id);
        $app = null;

        if($app_user_id != null)
        {
            $app = DB::table('oauth_tokens')
                ->join('app_keys','oauth_tokens.app_owner_id','=','app_keys.owner_id')
                ->where('app_user_id',$app_user_id)
                ->first();
        }
        else
        {
            $app = DB::table('app_keys')
                ->join('oauth_tokens','app_keys.user_id','=','oauth_tokens.user_id')
                ->first();
        }

        dd($app);

        $middleware = new Oauth1([

            'consumer_key' => $app->discogs_consumer_key,
            'consumer_secret' => $app->discogs_consumer_secret,

            //'consumer_key' => $app->consumer_key,
            //'consumer_secret' => $app->consumer_secret,

            //'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            //'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            //'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),
            //'callback'=>"https://localhost:8080",

            'callback'=>$app->callback_url.'/'.$app_user_id,

            'token' => $oauth_token,
            'token_secret' => $oauth_token_secret,
            'verifier' => $oauth_verifier
        ]);

        return $middleware;

    }

    //public static function httpRequest($method,$dir,$authenticated=false,$q='',$oauth_token='',$oauth_token_secret='',$oauth_verifier='')
    public static function httpRequest($method,$dir,$authenticated=false,$q='',$app_user_id='')
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        //$middleware = self::oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier);
        $middleware = self::oauthHeader($app_user_id);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => $BASE_URL,
            'handler' => $stack,
        ]);

        $error = null;
        $res = null;

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
                    //$listing_id=$q['listing_id'];
                    $res = $client->request('DELETE',$dir,['auth' => 'oauth','header' => self::contentHeader($authenticated)]);
                    break;
                
            }

            if($res->getStatusCode()!=200 && $res->getStatusCode()!=201 && $res != null)
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
            if($error != null)
            {
                //echo($error."\n");
            }
        }
        //dd($res->getBody());
        //dd(response()->json(["Error"=>$error,"Response"=>$res]));
        return ["Error"=>$error,"Response"=>$res];
    }

    

}