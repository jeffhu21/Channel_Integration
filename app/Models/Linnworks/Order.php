<?php

namespace App\Models\Linnworks;

class Order 
{
    //Order Structure in Linnworks
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
        'ReferenceNumber'=>'', //Order ID in Discogs
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
    'IsService'=>false,

    'OrderLineNumber'=>'', //Order Item ID
    'SKU'=>'', //Order Item Release ID
    'PricePerUnit'=>'',
    'Qty'=>'',
    'TaxRate'=>'',
    'LinePercentDiscount'=>0,
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

    //DiscountType
    public $DiscountType=[

        /// <summary>
        /// The given discount amount will be split evenly across all items and any applicable postage
        /// </summary>
        'AllEvenly'=>'AllEvenly',
        /// <summary>
        /// The discount amount will be split evenly across all items. Any remaining discount will be applied to the postage where applicable
        /// </summary>
        'ItemsThenPostage'=>'ItemsThenPostage',
        /// <summary>
        /// The discount amount will be applied to the postage cost where applicable. Any remaining discount will be split evenly across all items
        /// </summary>
        'PostageThenItems'=>'PostageThenItems'
    

    ];

}



