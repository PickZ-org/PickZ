<div class="modal fade lookup-modal" tabindex="-1" role="dialog" aria-labelledby="LookupModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Look up product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div
                    class="form-group kt-form__group">
                    <div class="col-lg-12">
                        <select class="form-control kt-input select-remote-data"
                                name="product_id"
                                id="lookup_product_id"
                                data-remote-uri="{{ url('/') }}/products/find"
                                style="width:100%;"></select>
                    </div>
                </div>
                <div
                    class="form-group kt-form__group">
                    <div class="col-lg-12">
                        <select class="form-control kt-input selectpicker"
                                name="product_uom_id" id="lookup_stock_uom">
                            <option value="0">-</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Select</button>
            </div>
        </div>
    </div>
</div>
