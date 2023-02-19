<script>
    $('#lookup_product_id').on('change', function () {
        $('#product').val($(this).val());
        $('#product').trigger('input');
        fill_uom_selector($('#lookup_stock_uom'), $(this).val());
    });
</script>
