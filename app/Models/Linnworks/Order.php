<?php

namespace App\Models\Linnworks;

class Order 
{
    public $order=[

        'BillingAddress'=>[],
        'DeliveryAddress'=>[],
        'OrderItems'=>[],
        'ExtendedProperties'=>[],
        'Notes'=>[],

        
        'Site'=>'',
        'MatchPostalServiceTag'=>'',
        'MatchPaymentMethodTag'=>'',
        

        'PaymentStatus'=>'',
        'ChannelBuyerName'=>'',
        'ReferenceNumber'=>'',
        'ExternalReference'=>'',
        'SecondaryReferenceNumber'=>'',
        'Currency'=>'',
        'ReceivedDate'=>'',
        'DispatchBy'=>'',
        'PaidOn'=>'',
        'PostalServiceCost'=>'',
        'PostalServiceTaxRate'=>'',
        //Please see the DiscountType enum for an explanation on how to use the DiscountType and Discount fields to apply a top-level discount to orders
        'DiscountType'=>'',
        'Discount'=>'',
        'MarketplaceTaxId' =>'',
        'MarketplaceIoss'=>'',

    ];

    //Address
    public $address = [

        'FullName'=>'',
        'Company'=>'',
        'Address1'=>'',
        'Address2'=>'',
        'Address3'=>'',
        'Town'=>'',
        'Region'=>'',
        'PostCode'=>'',
        'Country'=>'',
        'CountryCode'=>'',
        'PhoneNumber'=>'',
        'EmailAddress'=>'',


    ];

    
    //OrderItem
    public $OrderItem=[

    'TaxCostInclusive'=>'',
    'UseChannelTax'=>'',
    'IsService'=>'',

    'OrderLineNumber'=>'',
    'SKU'=>'',
    'PricePerUnit'=>'',
    'Qty'=>'',
    'TaxRate'=>'',
    'LinePercentDiscount'=>'',
    'ItemTitle'=>'',
    'Options'=>[],

    ];

    //OrderItemOption
    public $OrderItemOption = [
    'Name'=>'',
    'Value'=>'',
    ];

    //OrderExtendedProperty
    public $OrderExtendedProperty=[
    'Name'=>'',
    'Value'=>'',
    'Type'=>'',
    ];

    //OrderNote
    public $OrderNote=[
    'Note'=>'',
    'NoteEntryDate'=>'',
    'NoteUserName'=>'',
    'IsInternal'=>'',
    ];

}



