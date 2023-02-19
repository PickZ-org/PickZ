<div class="modal fade order-modal" tabindex="-1" role="dialog" aria-labelledby="OrderModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order info - {{$task->order->order_no}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{$task->order->contact->name}}
                </p>
                <ul class="list-group list-group-flush">
                    @foreach($task->order->orderlines as $line)
                        @if($line->open_quantity > 0)
                            <li class="list-group-item">
                                <strong>{{$line->product->name}} </strong>
                                <br/><small>({{$line->open_quantity}} x {{$line->productuom->name}})</small>
                                <br/><small>SKU: {{$line->product->sku}}</small>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
