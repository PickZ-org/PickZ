<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Location label</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row mb-3">
        <div class="col-xl-12 text-center">
            <h1>{{$location->name}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 text-center">
            {!! '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($location->barcode, 'C128',4,200) . '" alt="barcode" style="max-width:100%;" />'; !!}
        </div>
    </div>
</div>
</body>
</html>
