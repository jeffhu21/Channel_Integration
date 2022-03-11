<?php

namespace App\Models\Linnworks;

class PostSaleOptions
{
    public $postsale=[
        'CanCancel'=>'',
        'CanCancelOrderLines'=>'',
        'CanCancelOrderLinesPartially'=>'',
        'AutomaticRefundOnCancel'=>'',
        'CanRefund'=>'',
        'CanAttachRefundToItem'=>'',
        'CanAttachRefundToService'=>'',
        'RefundShippingTypes'=>'',
        'CanRefundAdditionally'=>'',
        'CanReturn'=>''
    ];

    public $Type =[
        'NotSupported'=>0,
        'TiedToItem'=>1,
        'Independent'=>2
    ]; //ShippingRefundType
}
