<script>
    // Printer
    printer_setup();

    function writeToSelectedPrinter(dataToWrite) {
        labelPrinter.send(dataToWrite, undefined, errorCallback);
    }

    function printStockLabel() {
        let formData = new FormData(document.getElementById('sendForm'));
        $.ajax({
            url: "{{ url('/document/zpl/stock' ) }}",
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function (data) {
                if (data.zpl) {
                    // Success
                    if (undefined !== labelPrinter) {
                        labelPrinter.send(data.zpl, undefined, errorCallback);
                        toastr.success('Label sent to printer', 'Success');
                    } else {
                        toastr.error('Error connecting to printer', 'Error');
                    }
                } else {
                    // Show error info
                    toastr.error('Could not retrieve label template', 'Error');
                }
            },
            error: function (response) {
                if (typeof response.responseJSON.message !== 'undefined') {
                    toastr.error(response.responseJSON.message, 'Error');
                } else {
                    toastr.error('An unknown error occurred', 'Error');
                }
            }
        });
    }

    $('.select-stockgroup').on('select2:select', function (e) {
        if (e.params.data.newTag) {
            // Expiry date group is new, enable expiry date field
            $('.' + $(this).data('expiryinput')).prop('disabled', false);
        } else {
            $('.' + $(this).data('expiryinput')).prop('disabled', true);
        }
    });
</script>
