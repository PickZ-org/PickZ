<script>
    $('.generate-stock-group').on('click', function (e) {
        $filler = $($(this).data('filler'));
        $.ajax({
            url: "{{ url('/stockgroups/generate') }}/" + $(this).data('id'),
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
            type: 'GET',
            dataType: 'JSON',
            data: {},
            success: function (data) {
                if (data.success) {
                    // Success
                    $filler.val(data.stockgroup.group_no);
                    toastr.success(data.message, 'Success');
                } else {
                    // Show error info
                    toastr.error(data.message, 'Error');
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
    });
</script>
