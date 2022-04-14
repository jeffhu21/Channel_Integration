<?php

namespace App\Http\Controllers\Linnworks;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\OrderController as DiscogsOrderController;
use App\Http\Controllers\Linnworks\AppUserAccess as AppUserAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\Order as Order;

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
        $res = DiscogsOrderController::listOrders($filter,$request->PageNumber,$app_user->id);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ['Error'=>$error];
        }
        $stream = $res['Orders'];

        //Set Default Status Unpaid
        $PaymentStatus=$this->paymentStatus['Unpaid'];

        /** 
         * $order - one order from Discogs
         * $obj - one order pushing to Linnworks
         * $orders - array of orders pushing to Linnworks
         */
        $orders = [];

        //Loop each order of $stream->orders from Discogs and push to Linnworks $orders array
        foreach ($stream->orders as $order) 
        {
            //Set the payment status
            if($order->status == 'Payment Received'||
            $order->status == 'Shipped'||
            $order->status == 'Invoice Sent')
            {
                $PaymentStatus=$this->paymentStatus['Paid'];
            }
            else if(str_contains($order->status,'Cancelled'))
            {
                $PaymentStatus=$this->paymentStatus['Cancelled'];
            }

            $obj = new Order();

            //Split the shipping address to full name, address and phone number
            $str = $order->shipping_address;

            $address = preg_split("/[\n]+/",$str,-1,PREG_SPLIT_NO_EMPTY);

            if(isset($address[4]))
            {
                $phone = preg_split("/:\s/",$address[4]);
            }
            else
            {
                $phone = '';
            }

            //Get the Order from Discogs and set the order to Linnworks
            $obj->order = [
                'DeliveryAddress' => 
                [$obj->address=
                    [
                    
                    
                    'FullName' => (!isset($address[0]))?'':$address[0],
                    'Address1' => (!isset($address[1]))?'':$address[1],
                    'Town'=>(!isset($address[2]))?'':$address[2],
                    'Country'=>(!isset($address[3]))?'':$address[3],
                    'PhoneNumber' => (!isset($phone[1]))?'':$phone[1],
                    ],
                ],
                'BillingAddress' => 
                [$obj->address=
                    [
                    'Address1' => (!isset($address[5]))?'':$address[5],
                    ],
                ],
                'ChannelBuyerName' => $order->buyer->username,
                'Currency' => $order->total->currency,
                'DispatchBy' => date('Y-m-d H:i:s',strtotime($order->created.'+ 10 days')),
                'ExternalReference' => "",
                'ReferenceNumber' => $order->id,
                'MatchPaymentMethodTag' => "",
                'MatchPostalServiceTag' => "",
                'PaidOn' => $order->last_activity,
                'PaymentStatus' => $PaymentStatus,
                'PostalServiceCost' => $order->shipping->value,
                'PostalServiceTaxRate' => "",
                'ReceivedDate' => $order->last_activity,
                'Site' => '',
                'Discount' => '',
                'DiscountType' => '',
                'MarketplaceIoss' => "Discogs",
                'MarketplaceTaxId' => ""
            ];

            $j=0;

            foreach($order->items as $item)
            {
                $obj->OrderItem=[
                        //'IsService' => false,
                        'ItemTitle' => "",
                        'SKU' => $item->release->id, //SKU in Linnworks refers to Discogs release id
                        'LinePercentDiscount' => 0,
                        'PricePerUnit' => $item->price->value,
                        'Qty' => 1,
                        'OrderLineNumber' => $item->id, //Order Line Number in Linnworks refers to Discogs item id
                        'TaxCostInclusive' => true,
                        'TaxRate' => 13,
                        'UseChannelTax' => false
                ];
                $obj->order['OrderItems'][$j]=$obj->OrderItem; //Add each order 
                $j++;
            }

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

                /*
                $obj->OrderExtendedProperty=[
                    'Name' => "Prop".$order->tracking->career, 
                    'Type' => "Tracking Number", 
                    'Value' => $order->tracking->number
                ];
                $obj->order['ExtendedProperties']=$obj->OrderExtendedProperty;
        
                $obj->OrderNote=[
                    'IsInternal' => false,
                    'Note' => "Note - ",
                    'NoteEntryDate' => now(),
                    'NoteUserName' => "Channel"
                ];
                $obj->order['Notes']=$obj->OrderNote;
                */
            
            array_push($orders,$obj->order);
            
        }
        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Orders']->pagination->pages,'Orders'=>$orders]);
    }

    /**
         * Update the despatched orders' status in Discogs with the despatch details
         * @param Request $request - with AuthorizationToken, Orders[App\Models\Linnworks\OrderDespatch]
         * @return [String: $error, Orders[String: $error,String ReferenceNumber]]
    */
    public function despatch(Request $request)
    {
        $request_orders=$request->input('Orders');

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
                $error = $error.$res['Error']."\n";

                //$UpdateFailedOrder = $UpdateFailedOrder.$res["ReferenceNumber"].", ";
                array_push($UpdateFailedOrders,['Error'=>$res['Error'],'ReferenceNumber'=>$res['ReferenceNumber']]);
            }
        }
        
        return SendResponse::httpResponse(["Error"=>$error,"Orders"=>$UpdateFailedOrder]);
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
