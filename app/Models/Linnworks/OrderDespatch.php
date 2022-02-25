<?php

namespace App\Models\Linnworks;

class OrderDespatch 
{
    public $OrderDespatch=[

        'ReferenceNumber'=>'',
        'ShippingVendor'=>'',
        'ShippingMethod'=>'',
        'TrackingNumber'=>'',
        'SecondaryTrackingNumbers'=>'',
        'ProcessedOn'=>'',
        'Items'=>[],

    ];

    public $OrderDespatchItem=[
        'SKU'=>'',
        'OrderLineNumber'=>'',
        'DespatchedQuantity'=>0,
    ];


}



