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

        .mb-1 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="mb-1">
    <div class="">
        <strong>{{$stock->product->name}}</strong>
    </div>
    <div class="">
        {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($stock->product->barcode, 'C128',2,50,[0,0,0],true) . '" alt="barcode" style="max-width:100%;" />'; !!}
    </div>
    <div>
        @if($stock->product->sku)
            <small>SKU: {{$stock->product->sku}}</small>
        @endif
        @if($stock->product->ean)
            <small>EAN: {{$stock->product->ean}}</small>
        @endif
    </div>
</div>
@if($stock->stockgroups()->exists())
    @foreach($stock->stockgroups as $stockgroup)
        <div class="mb-1">
            <div>
                {{$stockgroup->type->id_name}}: <strong>{{$stockgroup->group_no}}</strong> @if($stockgroup->expiry_date) <br /><small>{{$stockgroup->expiry_date}}</small> @endif
            </div>
            <div class="">
                {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($stockgroup->barcode, 'C128',2,50,[0,0,0],true) . '" alt="barcode" style="max-width:100%;" />'; !!}
            </div>
        </div>
    @endforeach
@endif
</body>
</html>
