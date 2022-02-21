<?php

namespace App\Models\Linnworks;


class ConfigItemListItem
{
    public ?String $Display,$Value;

    public function __construct(?String $Display=null,?String $Value=null)
    {
        $this->Display=$Display;
        $this->Value=$Value;
    }

}
