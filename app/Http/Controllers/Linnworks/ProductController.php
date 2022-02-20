<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function products()
    {
        return 'Products';
    }

    public function inventoryUpdate()
    {


        return [
            
                'Error Message',
                'SKU Message',
            
        ];
    }

    public function priceUpdate()
    {
        return 'Price Update';
    }
}
