<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\discogs\OAuthController as OAuthController;
use App\Http\Controllers\linnworks\AuthController as AuthController;
use App\Http\Controllers\linnworks\ConfigController as ConfigController;
use App\Http\Controllers\linnworks\OrderController as OrderController;
use App\Http\Controllers\linnworks\ProductController as ProductController;
use App\Http\Controllers\linnworks\ListingController as ListingController;
use App\Http\Controllers\linnworks\ConfiguratorSettings as ConfiguratorSettings;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Added
Route::controller(OAuthController::class)->group(

    function()
    {
        Route::get('/postback/{token}', 'saveLinnworksAuthToken');
        Route::get('/application/{id}/{secret}/{token}','saveLinnworksApplication');
        Route::get('/DiscogsSetting','discogsSetting');
    }
);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ConfigController::class)->prefix('Config')->group(

    function()
    {
        /*
        Route::get('/AddNewUser','addNewUser');
        Route::get('/UserConfig','userConfig');
        Route::get('/SaveUserConfig','saveConfig');
        Route::get('/ShippingTags','shippingTags');
        Route::get('/PaymentTags','paymentTags');
        Route::get('/ConfigDeleted','deleted');
        Route::get('/ConfigTest','test');
        */

        
        Route::post('/AddNewUser','addNewUser');
        Route::post('/UserConfig','userConfig');
        Route::post('/SaveUserConfig','saveConfig');
        Route::post('/ShippingTags','shippingTags');
        Route::post('/PaymentTags','paymentTags');
        Route::post('/ConfigDeleted','deleted');
        Route::post('/ConfigTest','test');
        
    }

);

Route::controller(OrderController::class)->prefix('Order')->group(

    function()
    {  
        //Route::get('/Orders','SampleOrders');
        /*
        Route::get('/Orders','orders');
        Route::get('/Despatch','despatch');
        */
        
        Route::post('/Orders','orders');
        Route::post('/Despatch','despatch');
        
        
    }

);

Route::controller(ProductController::class)->prefix('Product')->group(

    function()
    {
        /*
        Route::get('/Products','products');
        Route::get('/InventoryUpdate','inventoryUpdate');
        Route::get('/PriceUpdate','priceUpdate');
        */
        
        Route::post('/Products','products');
        Route::post('/InventoryUpdate','inventoryUpdate');
        Route::post('/PriceUpdate','priceUpdate');
        
    }

);

Route::controller(ListingController::class)->prefix('Listing')->group(

    function()
    { 
        //Route::get('/PostSaleOptions','postSaleOptions');


        /*
        Route::get('/GetConfiguratorSettings','getConfiguratorSettings');
        Route::get('/GetCategories','getCategories');
        Route::get('/GetAttributesByCategory','getAttributesByCategory');
        Route::get('/GetVariationsByCategory','getVariationsByCategory');
        */

        /*
        Route::get('/ListingUpdate','listingUpdate');
        Route::get('/ListingDelete','listingDelete');
        */
        //Route::get('/CheckFeed','checkFeed');
        
        
        
        Route::post('/ListingUpdate','listingUpdate');
        Route::post('/ListingDelete','listingDelete');
        
        //Route::post('/CheckFeed','checkFeed');
        
    }

);

Route::controller(ConfiguratorSettings::class)->prefix('Setting')->group(

    function()
    {
        /*
        Route::get('/GetConfiguratorSettings','getConfiguratorSettings');
        Route::get('/GetCategories','getCategories');
        Route::get('/GetAttributesByCategory','getAttributesByCategory');
        Route::get('/GetVariationsByCategory','getVariationsByCategory');
        
        Route::get('/CheckFeed','checkFeed');
        */

        
        Route::post('/GetConfiguratorSettings','getConfiguratorSettings');
        Route::post('/GetCategories','getCategories');
        Route::post('/GetAttributesByCategory','getAttributesByCategory');
        Route::post('/GetVariationsByCategory','getVariationsByCategory');

        Route::post('/CheckFeed','checkFeed');
        
        
    }

);



