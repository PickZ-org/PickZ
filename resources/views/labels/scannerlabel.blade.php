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
        <strong>{{$product->name}}</strong>
    </div>
    <div class="">
        {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->barcode, 'C128',2,50,[0,0,0],true) . '" alt="barcode" style="max-width:100%;" />'; !!}
    </div>
    <div>
        @if($product->sku)
            <small>SKU: {{$product->sku}}</small>
        @endif
        @if($product->ean)
            <small>EAN: {{$product->ean}}</small>
        @endif
    </div>
</div>
@if($stockGroupTypes->count())
    @foreach($stockGroupTypes as $stockGroupTypeId => $stockGroupType)
        <div class="mb-1">
            <div>
                {{ \App\Models\StockGroupType::find($stockGroupTypeId)->id_name }}: <strong>{{$stockGroupType['group_no']}}</strong> @if(isset($stockGroupType['expiry_date'])) <br /><small>{{$stockGroupType['expiry_date']}}</small> @endif
            </div>
            <div class="">
                {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($stockGroupType['group_no'], 'C128',2,50,[0,0,0],true) . '" alt="barcode" style="max-width:100%;" />'; !!}
            </div>
        </div>
    @endforeach
@endif
</body>
</html>
