<?php

namespace App\Models\Linnworks;


class ConfiguratorSetting
{
    
    public $response = 
        [ 
            "Error"=> null,
            "Settings"=> [],
            "MaxDescriptionLength"=>0,
            "ImageSettings"=> null, //ListingImage
            "MaxCategoryCount"=>0,
            "MaxCustomAttributeLength"=>0,
            "IsCustomHtmlSupported"=>false,
            "IsCustomAttributesAllowed"=>false,
            "IsVariationsAllowed"=>false,
            "HasMainVariationPrice"=>false,
            "IsTitleInVariation"=>false,
            "HasVariationAttributeDisplayName"=>false,
            "IsPriceInVariation"=>false,
            "IsShippingListingSpecific"=>false,
            "IsPaymentListingSpecific"=>false
        ];

    public $CustomerSettings = [
        "GroupName"=> "",//String, GroupNameValueType
        "ConfigItemId"=> "", //String
        "Subtitle"=> "", //String
        "SubTitleSortOrder"=> 0, 
        "ItemSortOrder"=> 0,
        "Description"=> "",
        "FriendlyName"=> "",
        "MustBeSpecified"=> false,
        "ExpectedType"=> "", //String, ListingValueType
        "ValueOptions"=> [],
        "InitialValues"=> [],
        "IsMultiOption"=> false,
        "ValueFromOptionsList"=> false,
        "RegExValidation"=> "",
        "RegExError"=> "",
        "IsWizardOnly"=> false
    ];

    public $ListingImage = [
        "Type"=>0,//Integer, ImageListingType
        "MaxImages"=>0,
        "MaxVariantImages"=>0,
        "ImageTags"=>[] //ImageTags
    ];

    public $ImageTag = [
        "Name"=>"",
        "ImageTagType"=>1 //Integer, ImageTagType
    ];

    public $GroupNameValueType = [
        "1"=>"GENERAL",
        "2"=>"RETURN",
        "3"=>"SHIPPING",
        "4"=>"PAYMENT",
        "5"=>"VARIATION"
    ];

    public $ListingValueType = [
        "1"=>"STRING",
        "2"=>"INT",
        "3"=>"DECIMAL",
        "4"=>"BOOL",
        "5"=>"DATETIME",
        "6"=>"LIST"
    ];

    public $ImageListingType = [
        "CountTogether"=>1,
        "CountMainVariantsSeparately"=>2,
        "SelectVariationFromMain"=>3
    ];

    

    public $ImageTagType = [
        "SingleTag"=>1,
        "MultiTag"=>2
    ];
        
}
