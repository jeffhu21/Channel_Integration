<?php

namespace App\Http\Controllers\discogs;

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

//use App\Models\Linnworks\UserInfo as UserInfo;

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
    public static function oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier)
    {
        $middleware = new Oauth1([

            'consumer_key' => Config::get('discogsAuth.CONSUMER_KEY'),
            'consumer_secret' => Config::get('discogsAuth.CONSUMER_SECRET'),

            'nonce'=>uniqid('linnworks_'),

            //'signature_method'=>"PLAINTEXT",
            'signature_method'=>"HMAC-SHA1",
            'timestamp'=>now()->format('YmdHis'),
            'callback'=>"https://localhost:8080",

            'token' => $oauth_token,
            'token_secret' => $oauth_token_secret,
            'verifier' => $oauth_verifier
        ]);

        return $middleware;

    }

    

    public static function httpRequest($method,$dir,$authenticated=false,$q='',$oauth_token='',$oauth_token_secret='',$oauth_verifier='',$consumer_key='',$consumer_secret='')
    {
        $BASE_URL = 'https://api.discogs.com/';
        $stack = HandlerStack::create(); 

        $middleware = self::oauthHeader($oauth_token,$oauth_token_secret,$oauth_verifier);

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