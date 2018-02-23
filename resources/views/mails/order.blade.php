<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodConn.com Order</title>
    <style>
        h1 {
            color: red;
        }
        td{
            border-bottom:1px solid grey
        }
        

    </style>

</head>

<body>
    


    <table width='600px' style="border-collapse: collapse; border:2px solid grey; font-size: 16px; margin: 20px auto">
        <thead>
            <tr style="border-bottom:1px solid grey; text-align: left; margin-bottom: 10px;">
                <th  style="padding-left:10px;">
                    <h2 style="color: #727272">{{$vendorName}}</h2>
                    @if ($update)
                        <strong style="color: red">Update for Order #{{$orderId}}</strong>
                        <p>Placed on {{$dt}}</p>
                    @else
                        <strong>Order #{{$orderId}}</strong>
                        <p>Placed on {{$dt}}</p>
                    @endif
                    
                </th>
                <td colspan="2" style="padding-bottom:20px;">
                    <p style="margin-bottom:0px;">{{$account->company}}</p>
                    <p style="margin-bottom:0px; margin-top:0px">{{$account->address}}</p>
                    <p style="margin-bottom:0px; margin-top:0px">{{$account->city}}</p>
                    <p style="margin-bottom:0px; margin-top:0px">{{$account->state}} {{$account->zipcode}}</p>
                    <p style="margin-bottom:0px; margin-top:0px">Phone: {{$account->phone}}</p>
                    <p style="margin-bottom:0px; margin-top:0px">Email: {{$account->email}}</p>
                    
                </td>
                

            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom:1px solid grey; padding-top: 5px;">
                <th width="50%"  style="padding-top:10px; padding-bottom:10px;">
                    <strong>Item name</strong>
                </th>
                <th>
                    <strong>Quantity</strong>
                </th>
                <th>
                    <strong>Packaging</strong>
                </th>

            </tr>
            @foreach ($order as $item)
                <tr style="border-bottom:1px solid #e5e5e5;">
                <td style="padding-left:10px;">
                    <p style="margin-bottom:2px">{{$item->name}}</p>
                    <small style="color: #FABE4C">{{$item->note}}</small>
                </td>
                <td style="text-align:center;">
                    {{$item->quantity}}
                </td>
                <td style="text-align:center;">
                    {{$item->pack}}
                </td>

            </tr>


            @endforeach

            <tr>
                <td colspan="3">
                    <p  style="padding-left:10px;"><strong>Comments:</strong></p>
                    <div>
                        @foreach ($vendorNote as $note)
                            <p style="padding-left:10px;">{{$note->note}}</p>
                        @endforeach
                    </div>
                </td>
            </tr>
        </tbody>

    </table>

</body>

</html>
