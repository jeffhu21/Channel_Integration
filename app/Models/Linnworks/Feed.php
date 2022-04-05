<?php

namespace App\Models\Linnworks;

class Feed
{
    public $ProductFeed = [
        "Messages"=>[],
        "SKU"=>"",
        "ExternalListingId"=>"",
        "TemplateId"=>0,
        "URL"=>"",
        "ChannelReferences"=>[]
    ];

    public $ChannelReference = [
        "SKU"=>"",
        "Reference"=>""
    ];

    public $Message = [
        "Type"=>[],
        "Message"=>""
    ];

    public $MessageType = [
        "1"=>"Error",
        "2"=>"Warning",
        "3"=>"Recommendation"
    ];
}
