<?php

namespace App\Http\Controllers\linnworks;

use Illuminate\Http\Request;

/*
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
*/

class SendResponse
{
    public static function httpResponse($content)
    {
        $type='application/json';
        $length=strlen(json_encode($content));
        return response($content)->header('Content-Type',$type)->header('Content-Length',$length);
    }
}