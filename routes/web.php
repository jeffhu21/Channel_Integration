<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\discogs\OAuthController as OAuthController;
use App\Http\Controllers\discogs\OrderController as OrderController;
use App\Http\Controllers\discogs\ProductController as ProductController;
//use App\Http\Controllers\linnworks\ConfigController as ConfigController;

use App\Http\Controllers\Discogs\ConfiguratorSettings as DiscogsConfiguratorSettings;

use Carbon\Carbon;

use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',function(){
    return view('home');
});

Route::get('storage/{filename}',function($filename)
{
    $path = storage_path('app/public/'.$filename);

    if(!File::exists($path))
    {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file,200);
    $response->header("Content-Type",$type);

    return $response;

    //return response()->download(storage_path('app/public/'.$filename));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/*
Route::get('/DiscogsOauth',function(){
    return view('DiscogsOauth');
})->middleware('auth')->name('DiscogsOauth');
*/


Route::controller(OAuthController::class)->group(

    function()
    {
        //Route::get('/EmailTest/{email}','EmailTest')->name('EmailTest');

        Route::get('/AppKey',function(){
            return view('AppKey');
        })->middleware('auth')->name('AppKey');

        Route::post('/AppKey','saveAppKey')->name('save_app_key');
        
        Route::get('/oauth_verifier','getVerifier');

        Route::get('/DiscogsOauth',function(){
            return view('DiscogsOauth');
        })
        //->middleware('auth')
        ->name('DiscogsOauthForm');
        
        Route::post('/DiscogsOauth','DiscogsOauth')->name('DiscogsOauth');

        /*
        Route::get('/request_token/{id?}','requestToken')->name('request_token');
        Route::get('/authorize/{oauth_token}','oauthAuthorize')->name('authorize');
        Route::get('/access_token','accessToken')->name('access_token');
        */

        //Route::post('/request_token','requestToken')->name('request_token');
        //Route::get('/authorize','oauthAuthorize')->name('authorize');
        //Route::post('/access_token','accessToken')->name('access_token');
        
    }
);

//Testing

Route::get('/orders/{id}',[OrderController::class,'getOrderById']);
Route::get('/list',[OrderController::class,'listOrders']);

Route::get('/inventory/{PageNumber}',[ProductController::class,'getInventory']);

Route::get('/Testing/{PageNumber}/{app_user_id}',[DiscogsConfiguratorSettings::class,'searchDB']); //Tester


require __DIR__.'/auth.php';

/*
Route::fallback(
    function(Request $request)
    {
        echo('Message');
        //echo($request->getURL());
    }
);
*/