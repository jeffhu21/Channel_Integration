<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\ProductController as DiscogsProductController;

use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\Product as Product;

use App\Models\OauthToken as OauthToken;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function products(Request $request)
    {
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getUserByToken($request);

        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];
        $error=null;

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;

        //Get Product from Discogs

        $res=DiscogsProductController::getInventory($request->PageNumber,$token,$token_secret);

        if($res['Error'] != null)
        {
            $error=$res['Error'];
            return ["Error"=>$error];
        }
        $products = [];
        //Could be changed with pagination

        

        //$productsCount = count($res['Products']->listings);
        
        foreach ($res['Products']->listings as $product) 
        {
            $obj = new Product();

            $obj->product = [
                
                'SKU'=>$product->release->id, 
                'Title'=>$product->release->title,
                'Price'=>$product->price->value,
                'Quantity'=>$product->format_quantity,
                'Reference'=>$product->id
            ];
            array_push($products,$obj->product);
        }  

        /*
        if($request->PageNumber < $res['Products']->pagination->pages)
        {
            $HasMorePages = true;
        }
        */

        //return ['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Products']->pagination->pages,'Products'=>$products];
        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Products']->pagination->pages,'Products'=>$products]);
    }

    public function inventoryUpdate(Request $request)
    {
        $request_products=json_decode($request->Products);
    
        if($request->Products == null || count($request_products) == 0)
        {
            return ['Error' => "Products not supplied"];
        }
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        //$user = $result['User'];
        //$HasError = false;
        $UpdateInventory = "";
        $error=null;

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        
        $inventory_product = [
            'Error'=>'',
            'SKU'=>''
        ];
        
        $products = [];

        foreach ($request_products as $product) 
        {
            
            $res=DiscogsProductController::updateInventory($product,$token,$token_secret,$token_verifier);

            /*
            if($res['Error'] != null)
            {
                $error = $error.$res['Error']."\n";
                $UpdateInventory = $UpdateInventory.$res["SKU"].", ";   
            }    
            */
            $inventory_product['Error'] = $res['Error'];
            $inventory_product['SKU'] = $res['SKU'];
            array_push($products,$inventory_product);
        }
        
        //return ["Error"=>null,"Products"=>["Error"=>$error,"SKU"=>$UpdateInventory]];
        return SendResponse::httpResponse(["Error"=>null,"Products"=>$products]);
    }

    public function priceUpdate(Request $request)
    {
        $request_products=json_decode($request->Products);

        //dd($request->Products);
    
        if($request->Products == null || count($request_products) == 0)
        {
            return ['Error' => "Products not supplied"];
        }
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        //$user = $result['User'];

        $UpdatePrice = "";
        $error=null;

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        $inventory_product = [
            'Error'=>'',
            'SKU'=>''
        ];
        
        $products = [];

        foreach ($request_products as $product) 
        {
            
            $res=DiscogsProductController::updatePrice($product,$token,$token_secret,$token_verifier);

            /*
            if($res['Error'] != null)
            {
                $error = $error.$res['Error']."\n";
                $UpdatePrice = $UpdatePrice.$res["SKU"].", ";   
            }
            */

            $inventory_product['Error'] = $res['Error'];
            $inventory_product['SKU'] = $res['SKU'];
            array_push($products,$inventory_product);
            
        }
        
        //return ["Error"=>null,"Products"=>["Error"=>$error,"SKU"=>$UpdatePrice]];
        return SendResponse::httpResponse(["Error"=>null,"Products"=>$products]);
    }
}
