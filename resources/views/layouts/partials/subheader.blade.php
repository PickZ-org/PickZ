<!-- begin:: Subheader -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">@yield('title')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @if(count(Request::segments()) > 1)
                        @foreach(Request::segments() as $segment)
                            <li class="breadcrumb-item active">
                                {{ $segment }}
                            </li>
                        @endforeach
                    @endif
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end:: Subheader -->
