// Globals

var labelPrinter;
var errorCallback = function(errorMessage){
    console.log("Error: " + errorMessage);
}

function scanner_listen() {
    $('.firstfocus').focus();
    $('.scannerinput').on('input', function () {
        let inputVar = $(this).val();
        let validationVar = $(this).data('validation-value');
        let inputType = $(this).prop('type');
        let doCheck = false;
        let proceed = true;
        // if input is empty, do nothing
        if (inputVar === '') {
            return false;
        }
        // Only check numbers if the number has the same length
        if (validationVar != '' && validationVar !== undefined) {
            if (inputType != 'number') {
                doCheck = true;
            } else if (inputVar.length == validationVar.toString().length) {
                doCheck = true;
            } else {
                proceed = false;
            }
        }
        if (doCheck) {
            if (inputVar == validationVar) {
                toastr.success($(this).data('label') + ' correct');
            } else {
                proceed = false;
                $(this).val('');
                $(this).attr('placeholder', inputVar);
                toastr.error($(this).data('label') + ' incorrect');
            }
        }
        if (proceed) {
            if ($(this).data('uom-selector')) {
                fill_uom_selector($($(this).data('uom-selector')), $(this).val(), $(this));
            } else if ($(this).data('next-focus')) {
                // Go to the next input
                $($(this).data('next-focus')).focus();
            }
            if ($(this).data('filler')) {
                $($(this).data('filler')).val($(this).val());
                $(this).val('');
                $(this).attr('placeholder', inputVar);
            }
            if ($(this).data('submitter')) {
                // This is the final step, submit form
                // TODO: check all inputs before submitting
                $(this).closest('form').submit();
            }
        }
    });

    $('.submit-on-change').on('change', function () {
        $(this).closest('form').submit();
    });
}

function fill_uom_selector(select, barcode, original) {
    $.ajax({
        type: 'POST',
        url: APP_URL + '/productuoms/findbybarcode',
        headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
        data: {
            'barcode': barcode,
            'direction': 'inbound',
        },
        success: function (response) {
            select.empty();
            for (var i = 0; i < response.length; i++) {
                let id = response[i]['id'];
                let name = response[i]['name'];
                $option = select.append("<option value='" + id + "'>" + name + "</option>");
                if (response[i]['default'] == '1') {
                    select.val(id)
                    $option.prop('selected', 'selected');
                }
            }
            $('.display-product-name').text(response[0].product.name);
            if ($(original).data('next-focus')) {
                // Go to the next input
                $($(original).data('next-focus')).focus();
            }
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

// Select2 remote data
function init_select_remote() {
    $('.select-remote-data').each(function() {
        let tags = false;
        var idColumn = $(this).data('idcolumn');
        if($(this).data('tags')) {
            tags = true;
        }
        $(this).select2({
            tags: tags,
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
                        } else if (obj.barcode !== undefined) {
                            obj.id = obj.barcode;
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
                        data: $(this).data('searchdata')
                    }

                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            },
            placeholder: 'Search...',
        });
    });
}

function init_datepicker() {
    // Datepicker
    $('.form-datepicker').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    });
}

function printer_setup()
{
    //Get the default device from the application as a first step. Discovery takes longer to complete.
    BrowserPrint.getDefaultDevice("printer", function(device)
    {
        // Set the global var to the printer
        labelPrinter = device;
    }, function(error){
        console.log(error);
    })
}

$(window).ready(function () {
    $(document).on("keydown", "input:not(.allow-enter)", function (event) {
        return event.key != "Enter";
    });
    scanner_listen();
    init_select_remote();
    init_datepicker();
});

