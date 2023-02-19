<script>
    function printStockLabel() {
        let formData = new FormData(document.getElementById('sendForm'));
        if('' !== formData.get('product_barcode')) {
            const queryString = new URLSearchParams(formData).toString()
            window.open('{{ url('/') }}/scanner/label/stock/?' + queryString);
        } else {
            toastr.error('No product scanned', 'Error');
        }
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
