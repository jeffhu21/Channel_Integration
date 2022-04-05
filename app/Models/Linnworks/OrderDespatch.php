<?php

namespace App\Models\Linnworks;

//Not Used
class OrderDespatch 
{
    //Despatched Order from Linnworks
    public $order=[

        'ReferenceNumber'=>'',
        'ShippingVendor'=>'',
        'ShippingMethod'=>'',
        'TrackingNumber'=>'',
        'SecondaryTrackingNumbers'=>'',
        'ProcessedOn'=>'',
        'Items'=>[], //Despatch Item

    ];

    public $item=[
        'SKU'=>'',//
        'OrderLineNumber'=>'',
        'DespatchedQuantity'=>0,
    ];


}



