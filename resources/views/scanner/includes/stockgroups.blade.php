@if($stockGroupTypes->isNotEmpty())
    <div class="card-body">
        @foreach($stockGroupTypes as $stockGroupType)
            @if($stockGroupType->auto_generate)
                @if ($stockGroupType->expires)
                    <div class="form-group">
                        <label class="col-form-label"
                               for="stockgrouptype[{{$stockGroupType->id}}][group_no]">{{ __(ucfirst($stockGroupType->label_single)) }}</label>
                        <div class="input-group">
                            <input class="form-control"
                                   type="text"
                                   placeholder="{{$stockGroupType->id_name}}"
                                   data-expiryinput="input-expirydate-{{$loop->iteration}}"
                                   name="stockgrouptype[{{$stockGroupType->id}}][group_no]"
                                   id="stockgrouptype_{{$stockGroupType->id}}">
                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <a class="generate-stock-group" data-id="{{$stockGroupType->id}}"
                                                       data-filler="#stockgrouptype_{{$stockGroupType->id}}"
                                                       href="#new_stock_group"><i
                                                            class="fa fa-plus"></i></a>
                                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <input type="text"
                               class="form-control form-datepicker input-expirydate-{{$loop->iteration}}"
                               placeholder="Expiry date"
                               name="stockgrouptype[{{$stockGroupType->id}}][expiry_date]"
                               value=""/>
                    </div>
                @else
                    <div class="form-group">
                        <label class="col-form-label"
                               for="stockgrouptype[{{$stockGroupType->id}}][group_no]">{{ __(ucfirst($stockGroupType->label_single)) }}</label>
                        <div class="input-group">
                            <input class="form-control"
                                   type="text"
                                   placeholder="{{$stockGroupType->id_name}}"
                                   id="stockgrouptype_{{$stockGroupType->id}}"
                                   name="stockgrouptype[{{$stockGroupType->id}}][group_no]">
                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <a class="generate-stock-group" data-id="{{$stockGroupType->id}}"
                                                       data-filler="#stockgrouptype_{{$stockGroupType->id}}"
                                                       href="#new_stock_group"><i
                                                            class="fa fa-plus"></i></a>
                                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                @if ($stockGroupType->expires)
                    <div class="form-group">
                        <label class="col-form-label"
                               for="stockgrouptype[{{$stockGroupType->id}}][group_no]">{{ __(ucfirst($stockGroupType->label_single)) }}</label>
                        <select class="form-control select-remote-data select-stockgroup"
                                data-tags="true"
                                data-searchdata="{{$stockGroupType->id}}"
                                data-idcolumn="group_no"
                                data-expiryinput="input-expirydate-{{$loop->iteration}}"
                                name="stockgrouptype[{{$stockGroupType->id}}][group_no]"
                                data-remote-uri="{{ url('/') }}/stockgroups/find"
                                style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                        <input type="text"
                               class="form-control form-datepicker input-expirydate-{{$loop->iteration}}"
                               placeholder="Expiry date"
                               name="stockgrouptype[{{$stockGroupType->id}}][expiry_date]"
                               value=""/>
                    </div>
                @else
                    <div class="form-group">
                        <label class="col-form-label"
                               for="stockgrouptype[{{$stockGroupType->id}}][group_no]">{{ __(ucfirst($stockGroupType->label_single)) }}</label>
                        <select class="form-control select-remote-data"
                                data-tags="true"
                                data-searchdata="{{$stockGroupType->id}}"
                                data-idcolumn="group_no"
                                name="stockgrouptype[{{$stockGroupType->id}}][group_no]"
                                data-remote-uri="{{ url('/') }}/stockgroups/find"
                                style="width:100%;"></select>
                    </div>
                @endif
            @endif
        @endforeach
    </div>
@endif
