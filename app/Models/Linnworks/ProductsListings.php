<?php

namespace App\Models\Linnworks;

class ProductsListings
{

    public String $AuthorizationToken;
    public $Type =[
        'Create'=>0,
        'Update'=>1
    ]; //ListingUpdateType
    public $Listings = []; //ProductListing
    public $Settings = []; //ConfiguratorGeneralSetting

    public $ProductListing =[

        'ConfiguratorID'=>'',
        'TemplateID'=>'',
        'ExternalListingID'=>'',
        'SKU'=>'',
        'Title'=>'',
        'Description'=>'',
        'Quantity'=>'',
        'Images'=>[],//ProductImage
        'Price'=>'',
        'Categories'=>[],
        'ShippingMethods'=>[],//ProductShipping
        'Attributes'=>[],//ListingAttribute
        'Variations'=>[],//ProductVariation
        'VariationOptions'=>[],//ProductOption

    ];

    public $ProductImage =[
        'Url'=>'',
        'Tags'=>[]
    ];

    public $ProductShipping = [
        'Price'=>'',
        'ShippingMethodID'=>''
    ];
    
    public $ListingAttribute = [
        'IsCustomAttribute'=>'',
        'AttributeID'=>'',
        'AttributeValue'=>''
    ];

    public $ProductVariation = [
        'SKU'=>'',
        'Title'=>'',
        'Quantity'=>'',
        'Images'=>[],//ProductImage
        'Price'=>'',
        'OptionValues'=>[],//VariationOption
        'AttributeSettings'=>[]//ListingAttribute
    ];

    public $VariationOption = [
        'Value'=>[],//ProductOptionValue
        'Position'=>'',
        'Name'=>''
    ];

    public $ProductOptionValue = [
        'Position'=>'',
        'Value'=>''
    ];

    public $ProductOption = [
        'Values'=>[],//ProductOptionValue
        'Position'=>'',
        'Name'=>''
    ];

    public $ProductSettings =[
        'PaymentMethods'=>[],
        'GeneralSettings'=>[],//Setting
        'ReturnsSettings'=>[],//Setting
        'ShippingSettings'=>[],//Setting
        'PaymentSettings'=>[],//Setting
        'VariationSettings'=>[],//Setting
        'AttributeSettings'=>[],//Setting
    ];

    public $Setting = [
        'ID'=>'',
        'Value'=>[]
    ];

}
