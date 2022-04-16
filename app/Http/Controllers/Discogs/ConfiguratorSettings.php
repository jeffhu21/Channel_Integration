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

use App\Http\Controllers\Discogs\SendRequest as SendRequest;

class ConfiguratorSettings extends Controller
{
    //
    public static function searchDB($PageNumber,$app_user_id)
    {
        $dir = 'database/search';

        $q="?page=".$PageNumber;

        //$res = SendRequest::httpRequest('GET',$dir.$q,true,'',$app_user_id);
        $res = SendRequest::httpRequest('GET',$dir.$q."&catno=DGCD-24425",true,'',$app_user_id);

        //dd($res);

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
        //dd($stream->results[0]);
        return ["Error"=>$error,"Categories"=>$stream];

    }

}
