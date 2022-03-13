<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\OrderController as DiscogsOrderController;
use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
use App\Http\Controllers\Linnworks\SendResponse as SendResponse;
use App\Models\Linnworks\Order as Order;
use App\Models\Linnworks\OrderDespatch as OrderDespatch;

use App\Models\OauthToken as OauthToken;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function orders(Request $request)
    {
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        //$user = $result['User'];

        $error = null;

        $filter = 'created_after='.($request->UTCTimeFrom);//Could be converted to different time zone
        //dd($filter);

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;

        //Retrieve Orders from Discogs
        $res = DiscogsOrderController::listOrders($filter,$request->PageNumber,$token,$token_secret);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ['Error'=>$error];
        }
        $stream = $res['Orders'];

        $PaymentStatus=config('linnworksHelper.PaymentStatus.Unpaid');//Set Default Status Unpaid

        /**
         * Push Orders to Linnworks
         * @param orders - array of orders push to Linnworks
         */
        $orders = [];

        /**
         * @param order - order from Discogs
         */
        foreach ($stream->orders as $order) 
        {

            //Set the payment status
            if($order->status == 'Payment Received'||
            $order->status == 'Shipped'||
            $order->status == 'Invoice Sent')
            {
                $PaymentStatus=config('linnworksHelper.PaymentStatus.Paid');
            }
            else if(str_contains($order->status,'Cancelled'))
            {
                $PaymentStatus=config('linnworksHelper.PaymentStatus.Cancelled');
            }

            //Could be modified
            /**
             * @param obj - order pushing to Linnworks
             */
            $obj = new Order();

            //Split the shipping address to full name, address and phone number
            $str = $order->shipping_address;

            $pos = strpos($str,"\n");
            
            if($pos != false)
            {
                $FullName = substr($str,0,$pos);
                $str = substr($str,$pos+1);
            }

            $phone_pos = stripos($str,"PHONE");

            if($phone_pos != false)
            {
                $address = substr($str,0,$phone_pos);
                $str = substr($str,$phone_pos);
                $pos=0;
                
                while(is_numeric(substr($str,$pos,1))!=1)
                {
                    $pos++;
                }
                $str=substr($str,$pos);

                if($str != "")
                {
                    while(is_numeric(substr($str,$pos,1))==1||substr($str,$pos,1)=='-')
                    {
                        $pos++;
                    }
                    $PhoneNumber=substr($str,0,$pos);
                    $other = substr($str,$pos);
                }

            }

            //Setting order pushing to Linnworks
            $obj->order = [
                'DeliveryAddress' => [$obj->address=
                [
                    'Address1' => str_replace("\n"," ",$address),
                    'FullName' => $FullName,
                    'PhoneNumber' => $PhoneNumber,
                    ],
                ],
                'BillingAddress' => [$obj->address=
                [
                    'Address1' => str_replace("\n"," ",$other),
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

            //$itemCount = count($order->items);
            $j=0;

            foreach($order->items as $item)
            {
                $obj->OrderItem=[
                        //'IsService' => false,
                        'ItemTitle' => "",
                        'SKU' => $item->release->id,
                        'LinePercentDiscount' => 0,
                        'PricePerUnit' => $item->price->value,
                        'Qty' => 1,
                        'OrderLineNumber' => $item->id,
                        'TaxCostInclusive' => true,
                        'TaxRate' => 13,
                        'UseChannelTax' => false
                ];
                $obj->order['OrderItems'][$j]=$obj->OrderItem;
                //array_push($obj->order['OrderItems'][$j],$obj->OrderItem);
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
            
            //$orders[$i]=$obj->order;
            array_push($orders,$obj->order);
            
        }
        //return ['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Orders']->pagination->pages,'Orders'=>$orders];
        return SendResponse::httpResponse(['Error'=>$error,'HasMorePages'=>$request->PageNumber < $res['Orders']->pagination->pages,'Orders'=>$orders]);
    }

    

    //
    /*
    public function SampleOrders(Request $request)
    {
        
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getUserByToken($request);
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
    

    public function despatch(Request $request)
    {
        $request_orders=json_decode($request->Orders);

        if($request->Orders == null || count($request_orders) == 0)
        {
            return ['Error' => "Invalid page number"];
        }
        
        $result = UserInfoAccess::getUserByToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];
        //$HasError = false;
        $UpdateOrder = "";
        $error=null;

        $record = OauthToken::first();
        $token = $record->oauth_token;
        $token_secret = $record->oauth_secret;
        $token_verifier=$record->oauth_verifier;

        //Push Updated Orders back to Discogs
        foreach ($request_orders as $order) 
        //for ($i=0; $i < count($request_orders); $i++) 
        { 
            /*
            $obj = new OrderDespatch();
            $obj->order=$order;
            $UpdateOrder=DiscogsOrderController::updateOrder($obj);
            */
            $res=DiscogsOrderController::updateOrder($order,$token,$token_secret,$token_verifier);
            
            if($res['Error'] != null)
            {
                //$HasError = true;
                $error = $error.$res['Error']."\n";
                $UpdateOrder = $UpdateOrder.$res["ReferenceNumber"].", ";


                //return $UpdateOrder;
            }
            
            //dd($obj->order);
        }
        
        //return ["Error"=>null,"Orders"=>["Error"=>$error,"ReferenceNumber"=>$UpdateOrder]];
        //return ["Error"=>null,"Orders"=>$res];
        return SendResponse::httpResponse(["Error"=>null,"Orders"=>["Error"=>$error,"ReferenceNumber"=>$UpdateOrder]]);
    }

}
