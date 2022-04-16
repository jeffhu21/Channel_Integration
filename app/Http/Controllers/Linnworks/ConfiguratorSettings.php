<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\ConfiguratorSettings as DiscogsConfiguratorSettings;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;

use App\Models\Linnworks\ConfiguratorSetting as ConfiguratorSetting;
use App\Models\Linnworks\Category as Category;
use App\Models\Linnworks\CategoryAttribute as CategoryAttribute;
use App\Models\Linnworks\CategoryVariation as CategoryVariation;
use App\Models\Linnworks\Feed as Feed;

use Illuminate\Http\Request;

class ConfiguratorSettings extends Controller
{
    /**
         * Provide settings for the listing screen layout in Linnworks
         * @return "App\Models\Linnworks\ConfiguratorSetting"
    */
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

    /**
         * Get a list of categories
         * @param Request $request - with AuthorizationToken, PageNumber
         * @return "App\Models\Linnworks\Category"
    */
    public function getCategories(Request $request)
    {
        
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = AppUserAccess::getUserByToken($request);

        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        $error=null;

        /*
        $res=DiscogsConfiguratorSettings::searchDB($request->PageNumber,$app_user->id);

        if($res['Error'] != null)
        {
            $error=$res['Error'];
            return ["Error"=>$error];
        }
        */
        
        $cat = new Category();

        /*
        $cat->ListingCategory=[
            //"CategoryId"=>0,
            //"CategoryName"=>''
        ];
        */

        //return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Categories']->pagination->pages,'Categories'=>$cat->ListingCategory]);
        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>false,'Categories'=>$cat->ListingCategory]);
    }

    /**
         * Get a list of required and optional attributes by category
         * @param Request $request - with AuthorizationToken, CategoryIds[], GeneralSettings[]
         * @return "App\Models\Linnworks\CategoryAttribute"
    */
    public function getAttributesByCategory(Request $request)
    {    
        //
        $request_ids = $request->input('CategoryIds'); 

        //Provided in GetConfiguratorSettings
        //GeneralSettings is an array[ID,Values]
        //ID is ConfigItemId in GetConfiguratorSettings and Values are chosen by customer
        $request_settings = $request->input('GeneralSettings'); 

        if($request->GeneralSettings == null || count($request_settings) == 0)
        {
            return ['Error' => "General Settings Not Found"];
        }

        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        $error=null;

        $attr = new CategoryAttribute();
        
        $attr->ListingCategoryAttribute=[

            "Error"=>null,
            "ID"=>"",
            "FriendlyName"=>"Free Shipping",
            "Description"=>"Free Shipping",
            "MustBeSpecified"=>$attr->MustBeSpecified['2'],
            "ExpectedType"=>$attr->ExpectedType['4'],
            "ValueOptions"=>["true","false"],
            "ValueFromOptionsList"=>true,
            "MaxAttributeUse"=>1,
            "AttributeReadFrom"=>$attr->AttributeReadFrom['2'],
            "RegExValidation"=>null,
            "RegExError"=>null

        ];
        

        return SendResponse::httpResponse(["Error"=>$error,"Attributes"=>$attr->ListingCategoryAttribute]);
    }

    /**
         * Get a list of required variation options by category
         * @param Request $request - with AuthorizationToken, CategoryIds[], GeneralSettings[]
         * @return "App\Models\Linnworks\CategoryVariation"
    */
    public function getVariationsByCategory(Request $request)
    {
        //
        $request_ids = $request->input('CategoryIds'); 

        //Provided in GetConfiguratorSettings
        //GeneralSettings is an array[ID,Values]
        //ID is ConfigItemId in GetConfiguratorSettings and Values are chosen by customer
        $request_settings = $request->input('GeneralSettings'); 

        if($request->GeneralSettings == null || count($request_settings) == 0)
        {
            return ['Error' => "General Settings Not Found"];
        }

        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        $error=null;

        $attr = new CategoryVariation();

        $attr->ListingCategoryVariation=[];

        return SendResponse::httpResponse(["Error"=>$error,"MaxVariationAttributes"=>50,"NeededVariations"=>$attr->ListingCategoryVariation]);
    }

    /**
         * Check status of submitted batch for listing creation, update or deletions
         * @param Request $request - with AuthorizationToken, ChannelFeedId
         * @return "App\Models\Linnworks\Feed"
    */
    public function checkFeed(Request $request)
    {
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        $error=null;

        //ChannelFeedId returned from ListingUpdate or ListingDelete
        $FeedId = $request->ChannelFeedId;


        
        return SendResponse::httpResponse(["Error"=>null,"Response"=>null]);
    }
}
