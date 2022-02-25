<?php

namespace App\Http\Controllers\Linnworks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Linnworks\UserInfoAccess as UserInfoAccess;
use App\Models\Linnworks\Order as Order;
use App\Models\Linnworks\OrderDespatch as OrderDespatch;

use Illuminate\Http\Request;

class OrderController extends Controller
{
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
                $obj->order['OrderItems']=$obj->OrderItem;
                //array_push($obj->order['OrderItems'],$obj->OrderItem);
            }

            for ($a = 0; $a < $randProps; $a++)
            {
                $obj->OrderExtendedProperty=[
                    'Name' => "Prop".$a, 
                    'Type' => "Info", 
                    'Value' => "Val".$a
                ];
                $obj->order['ExtendedProperties']=$obj->OrderExtendedProperty;
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
                $obj->order['Notes']=$obj->OrderNote;
                //array_push($obj->order['Notes'],$obj->OrderNote);
            }
            $orders=$obj->order;
            //array_push($orders,$obj->order);
        }

        return ['Error'=>$error,'HasMorePages'=>$request->PageNumber<11,'Orders'=>$orders];
        
    }

    public function order(Request $request)
    {
        
    }

    public function despatch(Request $request)
    {
        
        $result = UserInfoAccess::getAuthToken($request);
        if($result['Error'] != null)
        {
            return $result['Error'];
        }
        
        $user = $result['User'];
    }
}
