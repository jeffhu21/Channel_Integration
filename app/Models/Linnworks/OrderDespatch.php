<?php

namespace App\Models\Linnworks;

class OrderDespatch 
{
    public $order=[

        'ReferenceNumber'=>'',
        'ShippingVendor'=>'',
        'ShippingMethod'=>'',
        'TrackingNumber'=>'',
        'SecondaryTrackingNumbers'=>'',
        'ProcessedOn'=>'',
        'Items'=>[],

    ];

    public $item=[
        'SKU'=>'',
        'OrderLineNumber'=>'',
        'DespatchedQuantity'=>0,
    ];


}



