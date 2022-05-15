<?php

namespace App\Models\Linnworks;

//Not Used
class OrderDespatch 
{
    //Despatched Order from Linnworks
    //public $order=[

        public string $ReferenceNumber;
        public string $ShippingVendor;
        public string $ShippingMethod;
        public string $TrackingNumber;
        public string $SecondaryTrackingNumbers;
        public string $ProcessedOn;
        public $Items = [
            
        ]; //Despatch Item

    //];

    

    /*
    public $item=[
        'SKU'=>'',//
        'OrderLineNumber'=>'',
        'DespatchedQuantity'=>0,
    ];
    */


}



