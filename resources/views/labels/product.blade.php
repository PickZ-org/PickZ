<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Product label</title>
    <style>
        @page {
            size: {{\Configuration::get('label_width', '101.5mm')}} {{\Configuration::get('label_height', '152.4mm')}};
        }

        body {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="">
    <h3>{{$product->name}}</h3>
</div>
<div class="">
    {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->barcode, 'C128',2,100,[0,0,0],true) . '" alt="barcode" style="max-width:100%;" />'; !!}
</div>
<div>
    @if($product->sku)
        <p>SKU: {{$product->sku}}</p>
    @endif
    @if($product->ean)
        <p>EAN: {{$product->ean}}</p>
    @endif
</div>
</body>
</html>
