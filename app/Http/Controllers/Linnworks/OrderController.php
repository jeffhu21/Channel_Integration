<?php

namespace App\Http\Controllers\Linnworks;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\OrderController as DiscogsOrderController;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\Order as Order;

use App\Models\AppUser;
use App\Models\NotifyFailedDespatchedOrder as NotifyFailedDespatchedOrder;
use App\Models\NotifyFailedDespatchedItem as NotifyFailedDespatchedItem;

use Carbon\Carbon;

//use App\Models\OauthToken as OauthToken;

class OrderController extends Controller
{

    //Payment Status
    public $paymentStatus=[
        'Paid'=>'PAID',
        'Unpaid'=>'UNPAID',
        'Cancelled'=>'CANCELLED'
    ];

    /**
         * Retrieve all the orders in Discogs with pagination created after specified time of parameter UTCTimeFrom in Request and push to Linnworks
         * @param Request $request - with AuthorizationToken, UTCTimeFrom, PageNumber
         * @return [String: $error, Boolean: HasMorePages, $orders[App\Models\Linnworks\Order]]
    */
    public function orders(Request $request)
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

        $error = null;

        //Could be converted to different time zone
        //Retrieve orders from Discogs after UTCTimeFrom by sending request
        $filter = 'created_after='.($request->UTCTimeFrom);
        $res = DiscogsOrderController::listOrders($request->PageNumber,$app_user->id,$filter);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ['Error'=>$error];
        }
        $stream = $res['Orders'];

        

        /** 
         * $order - one order from Discogs
         * $obj - one order pushing to Linnworks
         * $orders - array of orders pushing to Linnworks
         */
        $orders = [];

        //Loop each order of $stream->orders from Discogs and push to Linnworks $orders array
        foreach ($stream->orders as $order) 
        {
            //Not push order with shipped status to Linnworks
            if($order->status != 'Shipped')
            {
                array_push($orders,$this->mapOrder($order,$app_user->id));
            }
            
        }
        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Orders']->pagination->pages,'Orders'=>$orders]);
    }

    public function OneOrder(Request $request)
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

        $error = null;

        //Could be converted to different time zone
        //Retrieve orders from Discogs after UTCTimeFrom by sending request
        $filter = 'created_after='.($request->UTCTimeFrom);
        $id = '2988670-22';
        $res = DiscogsOrderController::getOrderById($id,$app_user->id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ['Error'=>$error];
        }
        $stream = $res['Orders'];

        /** 
         * $order - one order from Discogs
         * $obj - one order pushing to Linnworks
         * $orders - array of orders pushing to Linnworks
         */
        $orders = [];
 
        //Not push order with shipped status to Linnworks
        if($stream->status != 'Shipped')
        {
            array_push($orders,$this->mapOrder($stream,$app_user->id));
        }

        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>false,'Orders'=>$orders]);
    }

    //$order - Discogs Order
    //Return Linnworks Order
    public function mapOrder($order,$app_user_id)
    {
        //$PaymentStatus=$this->paymentStatus['Unpaid'];

        //Set the payment status
        if($order->status == 'Payment Received')
        {
            $PaymentStatus=$this->paymentStatus['Paid'];
        }
        else if(str_contains($order->status,'Cancelled'))
        {
            $PaymentStatus=$this->paymentStatus['Cancelled'];
        }
        else
        {
            //Set Default Status Unpaid
            $PaymentStatus=$this->paymentStatus['Unpaid'];
        }

        $obj = new Order();

        //Split the shipping address to full name, address and phone number
        $str = $order->shipping_address;

        $address = preg_split("/[\n]+/",$str,-1,PREG_SPLIT_NO_EMPTY);

        //For phone number
        if(isset($address[4]))
        {
            $phone = preg_split("/:\s/",$address[4]);
        }
        else
        {
            $phone = '';
        }

        //For paypal address
        if(isset($address[5]))
        {
            $paypalAddress = preg_split("/:\s/",$address[5]);
        }
        else
        {
            $paypalAddress = '';
        }

        //Get the Order from Discogs and set the order to Linnworks
        $obj->order = [
            'BillingAddress' => 
            $obj->address=
                [
                'FullName' => '',
                'Company'=>'',
                'Address1' => '',
                'Address2' => '',
                'Address3' => '',
                'Town'=>'',
                'Region'=>'',
                'PostCode'=>'',
                'Country'=>'',
                'CountryCode'=>'',
                'PhoneNumber'=>'',
                'EmailAddress'=>(!isset($paypalAddress[1]))?'':$paypalAddress[1]
                ],
            

            'DeliveryAddress' => 
            $obj->address=
                [
                
                
                'FullName' => (!isset($address[0]))?'':$address[0],
                'Company'=>'',
                'Address1' => (!isset($address[1]))?'':$address[1],
                'Address2' => '',
                'Address3' => '',
                'Town'=>(!isset($address[2]))?'':$address[2],
                'Region' => '',
                'PostCode' => '',
                'Country'=>(!isset($address[3]))?'':$address[3],
                'CountryCode' => '',
                'PhoneNumber' => (!isset($phone[1]))?'':$phone[1],
                'EmailAddress' => ''
                ],
            
            
                //'OrderId'=>$order->id, //Added
                //'OrderStatus'=> $order->status,//Added
                
                'Site' => '',
                'MatchPostalServiceTag' => "",
                'MatchPaymentMethodTag' => "",
                'PaymentStatus' => $PaymentStatus,
                'ChannelBuyerName' => $order->buyer->username,
                'ReferenceNumber' => $order->id,
                'ExternalReference' => "",
                'SecondaryReferenceNumber'=>null,
                'Currency' => $order->total->currency,
                
                'ReceivedDate' => $order->last_activity,
                'DispatchBy' => date('Y-m-d H:i:s',strtotime($order->created.'+ 10 days')),
                'PaidOn' => $order->last_activity,
                
                /*
                'ReceivedDate' => date('Y-m-d H:i:s',strtotime($order->last_activity)),
                'DispatchBy' => date('Y-m-d H:i:s',strtotime($order->created.'+ 10 days')),
                'PaidOn' => date('Y-m-d H:i:s',strtotime($order->last_activity)),
                */
                'PostalServiceCost' => $order->shipping->value,
                'PostalServiceTaxRate' => "",
                'UseChannelTax' => false
            /*
            'Discount' => '',
            'DiscountType' => '',
            'MarketplaceIoss' => "Discogs",
            'MarketplaceTaxId' => ""
            */
        ];

        $j=0;

        foreach($order->items as $item)
        {
            //$res = DiscogsOrderController::getReleaseTitle($item->release->id);
            $res = DiscogsOrderController::getListingById($item->id,$app_user_id);

            if($res['Error'] != null)
            {
                $title = '';
                $catno = '';
            }
            else
            {
                $title = $res['Listing']->release->title;
                $catno = $res['Listing']->release->catalog_number;
            }
            
            
            $obj->OrderItem=[
                    'TaxCostInclusive' => true,
                    'UseChannelTax' => false,
                    'IsService' => false,
                    'OrderLineNumber' => $item->id, //Order Line Number in Linnworks refers to Discogs item id
                    'SKU' => $catno, //SKU in Linnworks refers to Discogs release catno
                    'PricePerUnit' => number_format($item->price->value,2),
                    //'PricePerUnit' => $item->price->value,
                    'Qty' => 1,
                    'TaxRate' => 13.0,//Different region has different rate
                    'LinePercentDiscount' => 0.0,
                    'ItemTitle' => $title,
                    'Options' => [
                            [
                                'Name'=>'',
                                'Value'=>'',
                            ]
                        ]
                    
            ];
            $obj->order['OrderItems'][$j]=$obj->OrderItem; //Add each order 
            $j++;
        }

        /*
        $randProps = rand(0, 2);
        $randNotes = rand(0, 2);

        for ($a = 0; $a < $randProps; $a++)
        {
            $obj->OrderExtendedProperty=[
                'Name' => "Prop".$a, 
                'Type' => "Info", 
                'Value' => "Val".$a
            ];
            $obj->order['ExtendedProperties'][$a]=$obj->OrderExtendedProperty;
        }

        for ($a = 0; $a < $randNotes; $a++)
        {
            $obj->OrderNote=[
                'IsInternal' => false,
                'Note' => "Note - ".$a,
                'NoteEntryDate' => now(),
                'NoteUserName' => "Channel"
            ];
            $obj->order['Notes'][$a]=$obj->OrderNote;
        }
        */

            
        $obj->OrderExtendedProperty=[
            [
            'Name' => "", 
            'Type' => "", 
            'Value' => ""
            ]
        ];
        $obj->order['ExtendedProperties']=$obj->OrderExtendedProperty;

        $obj->OrderNote=[
            [
            'IsInternal' => false,
            'Note' => "",
            'NoteEntryDate' => Carbon::parse($order->created),
            'NoteUserName' => $order->buyer->username
            ]
        ];
        $obj->order['Notes']=$obj->OrderNote;
        
        return $obj->order;
    }

    /**
         * Update the despatched orders' status in Discogs with the despatch details
         * @param Request $request - with AuthorizationToken, Orders[App\Models\Linnworks\OrderDespatch]
         * @return [String: $error, Orders[String: $error,String ReferenceNumber]]
    */
    public function despatch(Request $request)
    {
        //$request_orders=$request->input('Orders');
        $request_orders = json_decode($request->input('Orders'));

        if($request->Orders == null || count($request_orders) == 0)
        {
            return ['Error' => "No Despatched Orders"];
        }
        
        $result = AppUserAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $app_user = $result['User'];
        $UpdateFailedOrders = [];
        $error=null;

        //Update Despatched Orders in Discogs
        foreach ($request_orders as $order) 
        { 
            $res=DiscogsOrderController::updateOrder($order,$app_user->id);
            
            if($res['Error'] != null)
            {
                //$error = $error.$res['Error']."\n";

                $UpdateFailedOrder = $UpdateFailedOrder.$res["ReferenceNumber"].", ";
                array_push($UpdateFailedOrders,['Error'=>$res['Error'],'ReferenceNumber'=>$res['ReferenceNumber']]);
            
                //Save UpdateFailedOrders into Database for future notification
                $failedOrder = NotifyFailedDespatchedOrder::firstWhere('ReferenceNumber',$order->ReferenceNumber);

                
                if($failedOrder == null)
                {
                    $failedOrder=$app_user->NotifyFailedDespatchedOrder()->create([
                        //'AppUserId'=>$app_user->id,
                        'ReferenceNumber'=>$order->ReferenceNumber,
                        'ShippingVendor'=>$order->ShippingVendor,
                        'ShippingMethod'=>$order->ShippingMethod,
                        'TrackingNumber'=>$order->TrackingNumber,
                        //'SecondaryTrackingNumbers'=>$order->SecondaryTrackingNumbers,
                        'ProcessedOn'=>$order->ProcessedOn
                    ]);
                

                    foreach ($order->Items as $item) 
                    { 
                        
                        $failedItem=NotifyFailedDespatchedItem::create([
                            'ReferenceNumber'=>$failedOrder->ReferenceNumber,
                            'SKU'=>$item->SKU,
                            'OrderLineNumber'=>$item->OrderLineNumber,
                            'DespatchedQuantity'=>$item->DespatchedQuantity
                        ]);
                        
                    }

                    //dd($failedItem);
                }
            }
        }
        
        return SendResponse::httpResponse(["Error"=>$error,"Orders"=>$UpdateFailedOrders]);
    }

    
    /*
    public function SampleOrders(Request $request)
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
        
        $user = $result['User'];

        $error = null;

        $orders = [];

        $orderCount = 100;
        if ($request->PageNumber == 11)
        {
            $orderCount = 22;
        }
        else if ($request->PageNumber > 11)
        {
            $orderCount = 0;
        }

        for ($i = 1; $i <= $orderCount; $i++)
        {
            $obj = new Order();

            $obj->order = [
                'DeliveryAddress' => [$obj->address=
                [
                    'Address1' => "2-4 Southgate",
                    'Address2' => "",
                    'Address3' => "",
                    'Company' => "Linn Systems Ltd",
                    'Country' => "United Kingdom",
                    'CountryCode' => "GB",
                    'EmailAddress' => "test@test.com",
                    'FullName' => "Mr Testing Testington",
                    'PhoneNumber' => "00000000001",
                    'PostCode' => "PO19 8DJ",
                    'Region' => "West Sussex",
                    'Town' => "Chichester",
                    ],
                ],
                'BillingAddress' => [$obj->address=
                [
                    'Address1' => "2-4 Southgate",
                    'Address2' => "",
                    'Address3' => "",
                    'Company' => "Linn Systems Ltd",
                    'Country' => "United Kingdom",
                    'CountryCode' => "GB",
                    'EmailAddress' => "test@test.com",
                    'FullName' => "Mr Billing Billington",
                    'PhoneNumber' => "00000000002",
                    'PostCode' => "PO19 8DJ",
                    'Region' => "West Sussex",
                    'Town' => "Chichester",
                    ],
                ],
                'ChannelBuyerName' => "A Channel Buyer Name",
                'Currency' => "GBP",
                'DispatchBy' => now()->addDays(10),
                'ExternalReference' => "MyExternalReference-".($i * $request->PageNumber),
                'ReferenceNumber' => "MyReference-".(($i * $request->PageNumber) * 2),
                'MatchPaymentMethodTag' => "PayPal",
                'MatchPostalServiceTag' => "Royal Mail First Class",
                'PaidOn' => now()->addMinutes(rand(1, 10) * -1),
                'PaymentStatus' => config('linnworksHelper.PaymentStatus.Paid'),
                'PostalServiceCost' => 1+mt_rand()/mt_getrandmax()*(10-1),
                'PostalServiceTaxRate' => 20,
                'ReceivedDate' => now()->addMinutes(rand(1, 10) * -1),
                'Site' => '',
                'Discount' => 10,
                'DiscountType' => config('linnworksHelper.DiscountType.ItemsThenPostage'),
                'MarketplaceIoss' => "MarketPlaceIOSS",
                'MarketplaceTaxId' => "MarketPlaceTaxID"
            ];

            $randItems = rand(1, 10);
            $randProps = rand(0, 2);
            $randNotes = rand(0, 2);

            for ($a = 0; $a < $randItems; $a++)
            {
                $obj->OrderItem=[
                        'IsService' => false,
                        'ItemTitle' => "Title for ". $obj->order['ReferenceNumber']. "ChannelProduct_". ($a * $i),
                        'SKU' => "ChannelProduct_". ($a * $i),
                        'LinePercentDiscount' => 0,
                        'PricePerUnit' => 1+mt_rand()/mt_getrandmax()*(10-1),
                        'Qty' => rand(),
                        'OrderLineNumber' => strval($a * $i),
                        'TaxCostInclusive' => true,
                        'TaxRate' => 20,
                        'UseChannelTax' => false
                ];
                //dd($obj->order['OrderItems']);
                $obj->order['OrderItems'][$a]=$obj->OrderItem;
                //array_push($obj->order['OrderItems'],$obj->OrderItem);
            }

            for ($a = 0; $a < $randProps; $a++)
            {
                $obj->OrderExtendedProperty=[
                    'Name' => "Prop".$a, 
                    'Type' => "Info", 
                    'Value' => "Val".$a
                ];
                $obj->order['ExtendedProperties'][$a]=$obj->OrderExtendedProperty;
                //array_push($obj->order['ExtendedProperties'],$obj->OrderExtendedProperty);
            }

            for ($a = 0; $a < $randNotes; $a++)
            {
                $obj->OrderNote=[
                    'IsInternal' => false,
                    'Note' => "Note - ".$a,
                    'NoteEntryDate' => now(),
                    'NoteUserName' => "Channel"
                ];
                $obj->order['Notes'][$a]=$obj->OrderNote;
                //array_push($obj->order['Notes'],$obj->OrderNote);
            }
            $orders[$i]=$obj->order;
            //array_push($orders,$obj->order);
        }

        return ['Error'=>$error,'HasMorePages'=>$request->PageNumber<11,'Orders'=>$orders];
    }
    */
    
    

}
