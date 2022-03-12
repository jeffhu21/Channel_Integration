<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;

use App\Models\Linnworks\ConfiguratorSetting as ConfiguratorSetting;

use Illuminate\Http\Request;

class ConfiguratorSettings extends Controller
{
    public function getConfiguratorSettings()
    {
        $setting = new ConfiguratorSetting();

        $setting->response = [
            "Error"=> null,
            "Settings"=> [
            
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['5'], //VARIATION
            "ConfigItemId"=> "VariationTheme",
            "Subtitle"=> "General",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 1,
            "Description"=> "Theme used for variation. Cannot be changed once config created",
            "FriendlyName"=> "Variation Theme",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'],//STRING,
            "ValueOptions"=> ["Color", "Size", "Color-Size"],
            "InitialValues"=> [],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> true,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> true
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['2'], //RETURN
            "ConfigItemId"=> "ReturnCost",
            "Subtitle"=> "General:",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 1,
            "Description"=> "Please select who will cover return shipping cost",
            "FriendlyName"=> "Return paid by:",
            "MustBeSpecified"=> true,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> ["Buyer", "Seller"],
            "InitialValues"=> ["Buyer"],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> true,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['2'], //RETURN
            "ConfigItemId"=> "ReturnDays",
            "Subtitle"=> "General",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 2,
            "Description"=> "Please select number of days after sale return can be submitted within",
            "FriendlyName"=> "Return within:",
            "MustBeSpecified"=> true,
            "ExpectedType"=> $setting->ListingValueType['2'], //INT
            "ValueOptions"=> [7, 14, 30, 60, 90],
            "InitialValues"=> [14],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> true,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['3'], //SHIPPING
            "ConfigItemId"=> "ShippedFromCountry",
            "Subtitle"=> "General settings:",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 1,
            "Description"=> "",
            "FriendlyName"=> "Country:",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> ["United Kingdom", "USA"],
            "InitialValues"=> [],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> true,
            "RegExValidation"=> "",
            "RegExError"=> ""
            , "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['3'], //SHIPPING
            "ConfigItemId"=> "ShippedFromTown",
            "Subtitle"=> "General settings:",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 2,
            "Description"=> "",
            "FriendlyName"=> "Town:",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> [],
            "InitialValues"=> [],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> false,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['3'], //SHIPPING
            "ConfigItemId"=> "ShippedFromPostCode",
            "Subtitle"=> "General settings:",
            "SubTitleSortOrder"=> 1,
            "ItemSortOrder"=> 3,
            "Description"=> "",
            "FriendlyName"=> "Postcode:",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> [],
            "InitialValues"=> [],
            "IsMultiOption"=> false,
            "ValueFromOptionsList"=> false,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['3'], //SHIPPING
            "ConfigItemId"=> "DomesticShippingExcl",
            "Subtitle"=> "Domestic",
            "SubTitleSortOrder"=> 2,
            "ItemSortOrder"=> 1,
            "Description"=> "Enter post codes where you do not ship",
            "FriendlyName"=> "Shipping postcodes excluded:",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> [],
            "InitialValues"=> [],
            "IsMultiOption"=> true,
            "ValueFromOptionsList"=> false,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ],
            $setting->CustomerSettings = [
            "GroupName"=> $setting->GroupNameValueType['3'], //SHIPPING
            "ConfigItemId"=> "IntShippingExcl",
            "Subtitle"=> "International:",
            "SubTitleSortOrder"=> 3,
            "ItemSortOrder"=> 1,
            "Description"=> "Select countries where you do not ship",
            "FriendlyName"=> "Shipping countries excluded:",
            "MustBeSpecified"=> false,
            "ExpectedType"=> $setting->ListingValueType['1'], //STRING
            "ValueOptions"=> ["Republic of Ireland", "France", "Italy", "Spain", "Germany", "Austria","Switzerland", "Sweden" , "Norway", "Finland", "Denmark", "Estonia", "Latvia","Lithuania", "Poland"],
            "InitialValues"=> [],
            "IsMultiOption"=> true,
            "ValueFromOptionsList"=> true,
            "RegExValidation"=> "",
            "RegExError"=> "",
            "IsWizardOnly"=> false
            ]
        ],
            "MaxDescriptionLength"=>10000,
            "ImageSettings"=>
            $setting->ListingImage =
            [
            "Type"=>$setting->ImageListingType["CountMainVariantsSeparately"],//2
            "MaxImages"=>100,
            "MaxVariantImages"=>4,
            "ImageTags"=>
                [
                    $setting->ImageTag = 
                    [
                        "Name"=>"Main_image",
                        "ImageTagType"=>$setting->ImageTagType['MultiTag']//2
                    ],
                    $setting->ImageTag = 
                    [
                        "Name"=>"Large_image",
                        "ImageTagType"=>$setting->ImageTagType['MultiTag']//2
                    ],
                    $setting->ImageTag =
                    [
                        "Name"=>"Thumbnail_image",
                        "ImageTagType"=>$setting->ImageTagType['SingleTag']//1
                    ],
                    $setting->ImageTag =
                    [
                        "Name"=>"Basket_image",
                        "ImageTagType"=>$setting->ImageTagType['SingleTag']//1
                    ]
                ]
            ]
            ,
            "MaxCategoryCount"=>1000,
            "MaxCustomAttributeLength"=>1000,
            "IsCustomHtmlSupported"=>true,
            "IsCustomAttributesAllowed"=>true,
            "IsVariationsAllowed"=>true,
            "HasMainVariationPrice"=>true,
            "IsTitleInVariation"=>false,
            "HasVariationAttributeDisplayName"=>true,
            "IsPriceInVariation"=>true,
            "IsShippingListingSpecific"=>true,
            "IsPaymentListingSpecific"=>true
    ];

        return SendResponse::httpResponse($setting->response);
    }

    public function getCategories()
    {
        //return ["Error"=>null,"Response"=>null];
        return SendResponse::httpResponse(["Error"=>null,"Response"=>null]);
    }

    public function getAttributesByCategory()
    {
        //return ["Error"=>null,"Response"=>null];
        return SendResponse::httpResponse(["Error"=>null,"Response"=>null]);
    }

    public function getVariationsByCategory()
    {
        //return ["Error"=>null,"Response"=>null];
        return SendResponse::httpResponse(["Error"=>null,"Response"=>null]);
    }

    public function checkFeed()
    {
        //return ["Error"=>null,"Response"=>null];
        return SendResponse::httpResponse(["Error"=>null,"Response"=>null]);
    }
}
