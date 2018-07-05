<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to FoodConn</title>

 
</head>

<body>
<h1>Welcome to FoodConn.com</h1>
    
    
    @if ($email === 'listonly')
        <p>You requested price for items below</p>
        <ul>    
        @foreach ($list as $item)
            <li>{{$item['name']}}</li>
        @endforeach
        </ul>
    @else
        <p>{{$account->company}}, {{$account->city}} requested price for items click on <a href="http://localhost:4200/#/pricecheck/{{$hash}}">LINK</a></p>
         
    @endif

</body>

</html>