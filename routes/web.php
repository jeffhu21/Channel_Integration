<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\discogs\OAuthController as OAuthController;
use App\Http\Controllers\discogs\OrderController as OrderController;
use App\Http\Controllers\discogs\ProductController as ProductController;
//use App\Http\Controllers\linnworks\ConfigController as ConfigController;

use Carbon\Carbon;
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



Route::get('/', function () {
    return view('home1');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

//Route::get('/test',[OAuthController::class,'test']);
Route::get('/time',function()
{
    //return Config::get('discogsAuth.CONSUMER_KEY');
    return date('Y-m-d H:i:s',strtotime('2020-02-25T00:26:08.538899'.'+ 10 days'));
    
    //dd(date('Y-m-d H:i:s',strtotime('2020-02-25T00:26:08.538899'))->add(new DateInterval(P10D)));
    //return now();
});

//Route::get('/t0',[ConfigController::class,'addNewUser']);
//Route::get('/t1',[ConfigController::class,'UserConfig']);

Route::controller(OAuthController::class)->group(

    function()
    {
        //Route::get('/postback', 'saveLinnworksAuthToken');

        //Route::get('/request_token','requestToken')->name('request_token');
        Route::post('/request_token','requestToken')->name('request_token');
        Route::get('/oauth_verifier','oauthAuthorize')->name('oauth_verifier');
        Route::post('/access_token','accessToken')->name('access_token');
        /*
        Route::get('/username','getUsername');
        */

        //Route::get('/save_token','saveToken');
        //Route::get('/orders/{id}','getOrder');
        //Route::get('/orders','getAllOrders');
    }
);

Route::get('/orders/{id}',[OrderController::class,'getOrderById']);
Route::get('/list',[OrderController::class,'listOrders']);

Route::get('/inventory/{PageNumber}',[ProductController::class,'getInventory']);

Route::get('/Testing',[OrderController::class,'testing']); //Tester


require __DIR__.'/auth.php';

Route::get('/oauth')->missing(function(Request $request){
    echo('Message');
});

Route::fallback(
    function(Request $request)
    {
        echo('Message');
        //echo($request->getURL());
    }
);
