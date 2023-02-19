<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pick list | {{$order->order_no}}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="w-100">
    <div class="row">
        <div class="col-xl-12">
            <h3>Pick list | Order {{$order->order_no}}</h3>
            <small>{{$order->contact->name}}
                @if($order->contact->address2)
                    <br/>{{$order->contact->address2}}
                @endif
                @if($order->contact->address3)
                    <br/>{{$order->contact->address3}}
                @endif
                @if($order->contact->postalcode)
                    <br/>{{$order->contact->postalcode}}
                @endif
                @if($order->contact->city)
                    <br/>{{$order->contact->city}}
                @endif
                @if($order->contact->state)
                    <br/>{{$order->contact->state}}
                @endif
                @if($order->contact->country)
                    <br/>{{$order->contact->country}}
                @endif
            </small>
        </div>
    </div>
</div>
<div class="w-100">
    <div class="row">
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th>#</th>
                <th>Location</th>
                <th>Destination</th>
                <th>Product</th>
                <th>SKU</th>
                <th>UOM</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tasklines as $line)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$line->location->name}}</td>
                    <td>{{$line->destination->name}}</td>
                    <td>{{$line->product->name}}</td>
                    <td>{{$line->product->sku}}</td>
                    <td>{{$line->productuom->name}}</td>
                    <td>{{$line->quantity}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
