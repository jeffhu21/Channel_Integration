<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Discogs\OrderController as DiscogsOrderController;
use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
use App\Models\Linnworks\Order as Order;
use App\Models\Linnworks\OrderDespatch as OrderDespatch;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function orders(Request $request)
    {
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getAuthToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        $error = null;

        $filter = 'created_after='.($request->UTCTimeFrom);//Could be converted to different time zone
        //dd($filter);

        //Retrieve Orders from Discogs
        $res = DiscogsOrderController::listOrders($filter);

        if($res['Error'] != null)
        {
            $error = $res['Error'];
            return ['Error'=>$error];
        }
        $stream = $res['Response'];

        $PaymentStatus=config('linnworksHelper.PaymentStatus.Unpaid');//Set Default Status Unpaid
        
        /*
        Push Orders to Linnworks
        */
        $orders = [];

        $orderCount = count($stream->orders);
        
        /*
        if ($request->PageNumber == 11)
        {
            $orderCount = 22;
        }
        else if ($request->PageNumber > 11)
        {
            $orderCount = 0;
        }
        */

        //echo($stream->orders[0]->shipping_address);

        for ($i = 0; $i < $orderCount; $i++)
        {

            //Set the payment status
            if($stream->orders[$i]->status == 'Payment Received'||
            $stream->orders[$i]->status == 'Shipped'||
            $stream->orders[$i]->status == 'Invoice Sent')
            {
                $PaymentStatus=config('linnworksHelper.PaymentStatus.Paid');
            }
            else if(str_contains($stream->orders[$i]->status,'Cancelled'))
            {
                $PaymentStatus=config('linnworksHelper.PaymentStatus.Cancelled');
            }

            //$interval=new DateInterval('P10D');

            
            //dd($stream->orders[$i]->created);
            //dd(date('Y-m-d H:i:s',strtotime($stream->orders[$i]->created)));

            //Could be modified
            $obj = new Order();

            $str = $stream->orders[$i]->shipping_address;

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

                //dd("Phone Number: " . $PhoneNumber);
            }

            //$str=substr($FullName,$pos+1);

            /*
            $addr = [];

            $pos = strpos($address,"\n");

            //echo($address.'<br>');
            
            while ($pos!=false) 
            {
                //echo('Address: '.$address.'<br>');
                $addr[$index]=substr($address,0,$pos);
                $address=substr($address,$pos+1);
                //echo('Address Index: '.$addr[$index].'<br>');
                $index++;
                $pos = strpos($address,"\n");

            }
            $addr[$index] = $address;

            

            for($k=0;$k<count($addr);$k++)
            {
                echo('Addr: '.$addr[$k].'<br>');
            }

            echo('THE END <br>');
            */
            

            $obj->order = [
                'DeliveryAddress' => [$obj->address=
                [
                    //'Address1' => $stream->orders[$i]->shipping_address,
                    //'Address1' => str_replace("\n"," ",$stream->orders[$i]->shipping_address),
                    'Address1' => str_replace("\n"," ",$address),
                    'FullName' => $FullName,
                    'PhoneNumber' => $PhoneNumber,
                    /*
                    'Address2' => "",
                    'Address3' => "",
                    'Company' => "",
                    'Country' => "",
                    'CountryCode' => "",
                    'EmailAddress' => "",
                    'FullName' => "",
                    'PhoneNumber' => "",
                    'PostCode' => "",
                    'Region' => "",
                    'Town' => "",
                    */
                    ],
                ],
                'BillingAddress' => [$obj->address=
                [
                    'Address1' => str_replace("\n"," ",$other),
                    /*
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
                    */
                    ],
                ],
                'ChannelBuyerName' => $stream->orders[$i]->buyer->username,
                'Currency' => $stream->orders[$i]->total->currency,
                'DispatchBy' => date('Y-m-d H:i:s',strtotime($stream->orders[$i]->created.'+ 10 days')),
                'ExternalReference' => "",
                'ReferenceNumber' => $stream->orders[$i]->id,
                'MatchPaymentMethodTag' => "",
                'MatchPostalServiceTag' => "",
                'PaidOn' => $stream->orders[$i]->last_activity,
                'PaymentStatus' => $PaymentStatus,
                'PostalServiceCost' => $stream->orders[$i]->shipping->value,
                'PostalServiceTaxRate' => "",
                'ReceivedDate' => $stream->orders[$i]->last_activity,
                'Site' => '',
                'Discount' => '',
                'DiscountType' => '',
                'MarketplaceIoss' => "Discogs",
                'MarketplaceTaxId' => ""
            ];

            $itemCount = count($stream->orders[$i]->items);

            for ($j = 0; $j < $itemCount; $j++)
            {
                $obj->OrderItem=[
                        //'IsService' => false,
                        'ItemTitle' => $stream->orders[$i]->items[$j]->release->id,
                        'SKU' => '',
                        'LinePercentDiscount' => 0,
                        'PricePerUnit' => $stream->orders[$i]->items[$j]->price->value,
                        'Qty' => 1,
                        'OrderLineNumber' => $stream->orders[$i]->items[$j]->id,
                        'TaxCostInclusive' => true,
                        'TaxRate' => 13,
                        'UseChannelTax' => false
                ];
                $obj->order['OrderItems'][$j]=$obj->OrderItem;
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
                    'Name' => "Prop".$stream->orders[$i]->tracking->career, 
                    'Type' => "Tracking Number", 
                    'Value' => $stream->orders[$i]->tracking->number
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
            
            $orders[$i]=$obj->order;

            //echo(json_encode($orders));
            //dd($orders);
            
        }
        //dd($orders);
        //echo(json_encode($orders));
        return ['Error'=>$error,'HasMorePages'=>$request->PageNumber<11,'Orders'=>$orders];
        
    }

    //
    public function SampleOrders(Request $request)
    {
        
        if ($request->PageNumber <= 0)
        {
            return ['Error' => "Invalid page number"];
        }

        $result = UserInfoAccess::getAuthToken($request);
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

    

    public function despatch(Request $request)
    {
        $request_orders=json_decode($request->Orders);
        //dd(count($request_orders));
        if($request->Orders == null || count($request_orders) == 0)
        {
            return ['Error' => "Invalid page number"];
        }
        
        $result = UserInfoAccess::getAuthToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];

        //Push Updated Orders back to Discogs
        for ($i=0; $i < count($request_orders); $i++) 
        { 
            $obj = new OrderDespatch();
            $obj->order=$request_orders[$i];
            //dd($request_orders[$i]);
            $UpdateOrder=DiscogsOrderController::updateOrder($obj);
            
            if($UpdateOrder['Error'] != null)
            {
                return $UpdateOrder;
            }
            
            //dd($obj->order);
        }
        return ["Error"=>null,"Orders"=>$UpdateOrder];

    }

}
