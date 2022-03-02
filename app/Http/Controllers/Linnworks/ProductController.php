<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
use App\Models\Linnworks\Product as Product;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function products(Request $request)
    {
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getAuthToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        

    }

    public function inventoryUpdate()
    {

        /*
        return [
            
                'Error Message',
                'SKU Message',
            
        ];
        */
    }

    public function priceUpdate()
    {
        //return 'Price Update';
    }
}
