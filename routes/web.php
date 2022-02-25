<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\discogs\OAuthController as OAuthController;
use App\Http\Controllers\discogs\OrderController as OrderController;
//use App\Http\Controllers\linnworks\ConfigController as ConfigController;
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
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

//Route::get('/test',[OAuthController::class,'test']);
Route::get('/time',function()
{
    //return Config::get('discogsAuth.CONSUMER_KEY');
    return now();
});

//Route::get('/t0',[ConfigController::class,'addNewUser']);
//Route::get('/t1',[ConfigController::class,'UserConfig']);

Route::controller(OAuthController::class)->group(

    function()
    {
        Route::get('/request_token','requestToken')->name('request_token');
        Route::get('/oauth_verifier','oauthAuthorize')->name('oauth_verifier');
        Route::post('/access_token','accessToken')->name('access_token');

        //Route::get('/save_token','saveToken');
    }
);

Route::get('/orders/{id}',[OrderController::class,'getOrder']);


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
