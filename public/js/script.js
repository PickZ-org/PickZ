$(document).ready(function () {
    /**
     * Fix for select2 forms inside bootstrap modals
     * https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/19574076#19574076
     */
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    /**
     * Display render fixes
     */

    $('.repeater-headers').css('display', 'none');

    /**
     * Toastr setup
     */

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    /**
     * Plugin initiators
     */

    // Datepicker
    $('.form-datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        todayHighlight: true,
        autoclose: true
    });

    // Selectpicker
    //$('.selectpicker').selectpicker();

    // Form repeater
    $('.form-repeater').repeater({
        // (Optional)
        // start with an empty list of repeaters. Set your first (and only)
        // "data-repeater-item" with style="display:none;" and pass the
        // following configuration flag
        initEmpty: true,
        // (Optional)
        // "defaultValues" sets the values of added items.  The keys of
        // defaultValues refer to the value of the input's name attribute.
        // If a default value is not specified for an input, then it will
        // have its value cleared.
        defaultValues: {
            'text-input': ''
        },
        // (Optional)
        // "show" is called just after an item is added.  The item is hidden
        // at this point.  If a show callback is not given the item will
        // have $(this).show() called on it.
        show: function () {
            $(this).slideDown();
            init_select_remote();
            $(this).closest('.form-repeater').trigger('addline');
            $('.repeater-headers').fadeIn('slow');
            //$('.repeater-headers').css('visibility','visible');

        },
        // (Optional)
        // "hide" is called when a user clicks on a data-repeater-delete
        // element.  The item is still visible.  "hide" is passed a function
        // as its first argument which will properly remove the item.
        // "hide" allows for a confirmation step, to send a delete request
        // to the server, etc.  If a hide callback is not given the item
        // will be deleted.
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
            if ($('[data-repeater-item]').length == 1) {
                $('.repeater-headers').fadeOut('slow');
            }
        },
        // (Optional)
        // You can use this if you need to manually re-index the list
        // for example if you are using a drag and drop library to reorder
        // list items.
        ready: function (setIndexes) {
            //$dragAndDrop.on('drop', setIndexes);
        },
        // (Optional)
        // Removes the delete button from the first list item,
        // defaults to false.
        isFirstItemUndeletable: true
    });

    // Select2 remote data
    function init_select_remote() {
        $('.select-remote-data').each(function() {
            let tags = false;
            let placeholder = 'Search...';
            var idColumn = $(this).data('idcolumn');
            if($(this).data('tags')) {
                tags = true;
            }
            if($(this).data('placeholder')) {
                placeholder = $(this).data('placeholder');
            }
            $(this).select2({
                tags: tags,
                selectOnClose: false,
                createTag: function (params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true // add additional parameters
                    }
                },
                minimumInputLength: 1,
                width: 'resolve',
                ajax: {
                    delay: 250,
                    url: function () {
                        return $(this).data('remote-uri');
                    },
                    processResults: function (data) {
                        var data1 = $.map(data.results, function (obj, idx) {
                            if(obj[idColumn] !== undefined) {
                                obj.id = obj[idColumn] || idx;
                            }
                            return obj;
                        });
                        return {
                            results: data1,
                        };
                    },
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            data: $(this).data('searchdata'),
                            extra: $(this).data('extra')
                        }

                        // Query parameters will be ?search=[term]&page=[page]
                        return query;
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                placeholder: placeholder,
            });
        });
    }

    init_select_remote();

    /**
     * Enabling ajax submit for forms in modals
     */
    $('.btn-modal-form-submit').click(function () {
        const $form = $($(this).data('target'));
        const $modal = $form.closest('.modal');

        if ($(this).data('no-ajax')) {
            $form.submit();
        } else {
            $.ajax({
                type: $form.attr('method') || 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: $form.attr('action'), // the url where we want to POST
                data: $form.serialize(), // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                success: function (data) {
                    $modal.modal('hide');
                    if (data.success) {
                        // Success
                        //Reload any datatable
                        $('.dataTable').each(function () {
                            $(this).DataTable().ajax.reload();
                        });
                        $form.find('input.coldstockrowremove:checked').each(function () {
                            let maxQuantity = parseInt($(this).data('max-quantity'));
                            let inputQuantity = $(this).closest('tr').find('.csquantity').val();
                            if (maxQuantity <= inputQuantity) {
                                $(this).closest('tr').remove();
                            } else {
                                $(this).data('max-quantity', (maxQuantity - inputQuantity));
                                $(this).closest('tr').find('.csquantity').val(maxQuantity - inputQuantity);
                            }
                        });
                        toastr.success(data.message, 'Success');
                    } else {
                        // Show error info
                        toastr.error(data.message, 'Error');
                    }
                },
                error: function (response) {
                    $modal.modal('hide');
                    if (typeof response.responseJSON.message !== 'undefined') {
                        let errorMsg = response.responseJSON.message;
                        if (typeof response.responseJSON.errors !== 'undefined') {
                            for (var property in response.responseJSON.errors) {
                                errorMsg = errorMsg + '<br />' + response.responseJSON.errors[property][0];
                            }
                        }
                        toastr.error(errorMsg, 'Error');
                    } else {
                        toastr.error('An unknown error occurred', 'Error');
                    }
                }
            });
        }
    });

    $('.btn-inputtext').click(function(){
        $targetTextarea = $($(this).data('targettextarea'));
        let inputText = $(this).data('inputtext');
        let caretPos = $targetTextarea[0].selectionStart;
        let textAreaTxt = $targetTextarea.val();
        $targetTextarea.val(textAreaTxt.substring(0, caretPos) + inputText + textAreaTxt.substring(caretPos) );
        $targetTextarea.focus();
    });

});

function fill_uom_selector(select, productId, selected = false) {
    $.ajax({
        type: 'POST',
        url: APP_URL + '/productuoms/findbyproduct',
        headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
        data: {
            'product_id': productId,
            'direction': 'inbound',
        },
        success: function (response) {
            select.empty();
            for (var i = 0; i < response.length; i++) {
                let id = response[i]['id'];
                let name = response[i]['name'];
                $option = select.append("<option value='" + id + "'>" + name + "</option>");
                if (response[i]['default'] === '1' || response[i]['id'] === selected) {
                    select.val(id)
                    $option.prop('selected', 'selected');
                }
            }
            // select.selectpicker('refresh');
        },
        error: function (response) {
            if (typeof response.responseJSON.message !== 'undefined') {
                let errorMsg = response.responseJSON.message;
                if (typeof response.responseJSON.errors !== 'undefined') {
                    for (var property in response.responseJSON.errors) {
                        errorMsg = errorMsg + '<br />' + response.responseJSON.errors[property][0];
                    }
                }
                toastr.error(errorMsg, 'Error');
            } else {
                toastr.error('An unknown error occurred', 'Error');
            }
        },
        dataType: 'JSON'
    });
}
