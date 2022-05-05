<?php

namespace App\Http\Controllers\Linnworks;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\ProductController as DiscogsProductController;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\Product as Product;

//use App\Models\OauthToken as OauthToken;

class ProductController extends Controller
{
    
    /**
         * Retrieve all the products in Discogs with pagination and push to Linnworks
         * @param Request $request - with AuthorizationToken, PageNumber
         * @return [String: $error, Boolean: HasMorePages, $products[App\Models\Linnworks\Product]]
    */
    public function products(Request $request)
    {
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = AppUserAccess::getUserByToken($request);

        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];

        $error=null;

        //Retrieve products from Discogs
        $res=DiscogsProductController::getInventory($request->PageNumber,$app_user->id);

        if($res['Error'] != null)
        {
            $error=$res['Error'];
            return ["Error"=>$error];
        }

        /** 
         * $product - one product from Discogs
         * $obj - one product pushing to Linnworks
         * $products - array of products pushing to Linnworks
         */
        $products = [];
        
        //Loop each product of $res['Products']->listings from Discogs and push to Linnworks $products array
        foreach ($res['Products']->listings as $product) 
        {
            //push product with draft, for sale or expired status to Linnworks
            if($product->status == 'Draft' || $product->status == 'For Sale' || $product->status == 'Expired')
            {

            
            $obj = new Product();

            $obj->product = [
                
                'SKU'=>$product->release->catalog_number, //SKU in Linnworks refers to Discogs release catalog number
                'Title'=>$product->release->title,
                'Price'=>$product->price->value,
                //'Quantity'=>$product->format_quantity,
                'Quantity'=>$product->quantity,
                'Reference'=>$product->id //Reference in Linnworks refers to Discogs product id
            ];
            array_push($products,$obj->product);
            }
        }  

        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Products']->pagination->pages,'Products'=>$products]);
    }

    /**
         * Update the inventory in Discogs
         * @param Request $request - with AuthorizationToken, Products[App\Models\Linnworks\ProductInventory]
         * @return [String: $error, Products[String: Error,String: SKU]]
    */
    public function inventoryUpdate(Request $request)
    {
        //$request_products=$request->input('Products');
        $request_products = json_decode($request->input('Products'));
    
        if($request->Products == null || count($request_products) == 0)
        {
            return ['Error' => "Products not supplied"];
        }
        
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        //$UpdateInventory = "";
        $error=null;

        $inventory_product = [
            'Error'=>'',
            'SKU'=>''
        ];
        
        /** 
         * $product - one updated product from Linnworks
         * $UpdateFailedProducts - array of products failed to update in Discogs
         */
        $UpdateFailedProducts = [];

        foreach ($request_products as $product) 
        {
            //Update inventory in Discogs by sending request
            $res=DiscogsProductController::updateInventory($product,$app_user->id);

            
            if($res['Error'] != null)
            {
                $error = $error.$res['Error']."\n";
                //$UpdateInventory = $UpdateInventory.$res["SKU"].", ";   
                //$inventory_product['Error'] = $res['Error'];
                $inventory_product['Error'] = 'SKU updates failed';
                $inventory_product['SKU'] = $res['SKU'];

                array_push($UpdateFailedProducts,$inventory_product);
            }    
            
        }
        
        return SendResponse::httpResponse(["Error"=>$error,"Products"=>$UpdateFailedProducts]);
    }

    /**
         * Update the inventory price in Discogs
         * @param Request $request - with AuthorizationToken, Products[App\Models\Linnworks\ProductPrice]
         * @return [String: $error, Products[String: Error,String: SKU]]
    */
    public function priceUpdate(Request $request)
    {
        $request_products=$request->input('Products');
    
        if($request->Products == null || count($request_products) == 0)
        {
            return ['Error' => "Products not supplied"];
        }
        
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];

        //$UpdatePrice = "";
        $error=null;

        $inventory_product = [
            'Error'=>'',
            'SKU'=>''
        ];
        
        /** 
         * $product - one updated product from Linnworks
         * $UpdateFailedProducts - array of products failed to update in Discogs
         */
        $UpdateFailedProducts = [];

        foreach ($request_products as $product) 
        {
            //Update price in Discogs by sending request
            $res=DiscogsProductController::updatePrice($product,$app_user->id);

            
            if($res['Error'] != null)
            {
                $error = $error.$res['Error']."\n";
                //$UpdatePrice = $UpdatePrice.$res["SKU"].", ";  
                //$inventory_product['Error'] = $res['Error'];
                $inventory_product['Error'] = 'SKU does not exist';
                $inventory_product['SKU'] = $res['SKU'];
                array_push($UpdateFailedProducts,$inventory_product); 
            }
        }
        
        return SendResponse::httpResponse(["Error"=>null,"Products"=>$UpdateFailedProducts]);
    }
}
