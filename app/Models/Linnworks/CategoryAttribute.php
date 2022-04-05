<?php

namespace App\Models\Linnworks;

class CategoryAttribute
{
    public $ListingCategoryAttribute = [
        "Error"=>null,
        "ID"=>"",
        "FriendlyName"=>"",
        "Description"=>"",
        "MustBeSpecified"=>[],
        "ExpectedType"=>"",
        "ValueOptions"=>[],
        "ValueFromOptionsList"=>false,
        "MaxAttributeUse"=>0,
        "AttributeReadFrom"=>"",
        "RegExValidation"=>"",
        "RegExError"=>""
    ];

    public $MustBeSpecified = [
        "1"=>"Required",
        "2"=>"Desired"
    ];

    public $ExpectedType = [
        "1"=>"STRING",
        "2"=>"INT",
        "3"=>"DECIMAL",
        "4"=>"BOOL",
        "5"=>"DATETIME",
        "6"=>"LIST"
    ];

    public $AttributeReadFrom = [
        "1"=>"Child",
        "2"=>"Parent",
        "3"=>"Optional"
    ];
    
}
