<?php

return [
    'APPLICATION'=>[
        'ID'=>'3d0b2722-b4e8-4997-9d58-50df4b1f2e3f',
        'SECRET'=>'0586ee94-544c-44ad-9942-f02449ac8ec0',
    ],

    'ConfigValueType'=>[
        'String'=>'STRING',
        'Int'=>'INT',
        'Double'=>'DOUBLE',
        'Boolean'=>'BOOLEAN',
        'Password'=>'PASSWORD',
        'List'=>'LIST',
    ],

    'PaymentStatus'=>[
        'Paid'=>'PAID',
        'Unpaid'=>'UNPAID',
        'Cancelled'=>'CANCELLED'
    ],

    'DiscountType'=>[

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
    

    ]

    
    
    //'TOKEN' => '7575e973ed7305c9044fc70653843a63',
    
    //'IsOauth'=>'false',
    //'StepName'=>''
];