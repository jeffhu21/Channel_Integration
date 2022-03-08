<?php

namespace App\Models\Discogs;

class Listing
{
    public $listing = [
        'listing_id'=>0,
        'release_id'=>0,
        'condition'=>'',
        'sleeve_condition'=>'',
        'price'=>0,
        'comments'=>'',
        'allow_offers'=>false,
        'status'=>'',
        'external_id'=>'',
        'location'=>'',
        'weight'=>0,
        'format_quantity'=>0
    ];

    /*
    public $listing1=[
        'status'=>'',
        'price'=>[
            "currency"=>'',
            "value"=>0
        ],
        'original_price'=>[
            "curr_abbr"=>'',
            "curr_id"=>0,
            "formatted"=>'',
            "value"=>0
        ],
        "allow_offers"=> false,
        "sleeve_condition"=> "",
        "id"=>0,
        "external_id"=>0,
        "location"=>'',
        "weight"=>0,
        "format_quantity"=>0,
        "condition"=>'',
        "posted"=>'',
        "ships_from"=>'',
        "uri"=>'',
        "comments"=>'',
        'seller'=>[
            "username"=>'',
            "avatar_url"=>'',
            "resource_url"=>'',
            "url"=>'',
            "id"=>0,
            "shipping"=>'',
            "payment"=>"",
            "stats"=>[
                "rating"=>0,
                "stars"=>'',
                "total"=>0
            ]
        ],
        "shipping_price"=>[
            "currency"=>'',
            "value"=>0
        ],
        "original_shipping_price"=>[
            "curr_abbr"=>'',
            "curr_id"=>0,
            "formatted"=>'',
            "value"=>0
        ],
        "release"=>[
            "catalog_number"=>'',
            "resource_url"=>'',
            "year"=>'',
            "id"=>0,
            "description"=>'',
            "thumbnail"=>''
        ],
        "resource_url"=>'',
        "audio"=> false

    ];
    */

    public $condition = [
        'M'=>'Mint (M)',
        'NM'=>'Near Mint (NM or M-)',
        'VG+'=>'Very Good Plus (VG+)',
        'VG'=>'Very Good (VG)',
        'G+'=>'Good Plus (G+)',
        'G'=>'Good (G)',
        'F'=>'Fair (F)',
        'P'=>'Poor (P)',
        'Generic'=>'Generic'
    ];

    



}
