<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function orders()
    {
        return 'Orders';
    }

    public function despatch()
    {
        return 'Despatch';
    }
}
