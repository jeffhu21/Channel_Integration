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


/*
Route::get('/DiscogsSetting/{token?}',function(){
    return view('home');
})->name('home');
*/


Route::get('/',function(){
    return view('home1');
});

/*
Route::get('/AppKey',function(){
    return view('AppKey');
})->middleware('auth')->name('AppKey');
*/

Route::get('/DiscogsOauth',function(){
    return view('DiscogsOauth');
})->middleware('auth')->name('DiscogsOauth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/oauth_verifier',[OAuthController::class,'getVerifier']);

Route::controller(OAuthController::class)->middleware(['auth'])->group(

    function()
    {
        Route::get('/AppKey',function(){
            return view('AppKey');
        })->name('AppKey');
        Route::post('/AppKey','saveAppKey')->name('save_app_key');
        //Route::get('/request_token','requestToken')->name('request_token');
        Route::post('/request_token','requestToken')->name('request_token');
        Route::get('/authorize','oauthAuthorize')->name('authorize');
        Route::get('/access_token','accessToken')->name('access_token');
        //Route::post('/access_token','accessToken')->name('access_token');
        
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

/*
Route::fallback(
    function(Request $request)
    {
        echo('Message');
        //echo($request->getURL());
    }
);
*/