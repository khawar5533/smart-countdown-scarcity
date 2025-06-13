 // Used for upload banner image
 jQuery(function($){
        $('.upload_image_button').on('click', function(e){
            e.preventDefault();
            var button = $(this);
            var field = $('#wbgs_banner_image');
            var custom_uploader = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                field.val(attachment.url);
            }).open();
        });
    });
 // add or update hidden values using ajax
   jQuery(document).ready(function($) {
    $('#wbgs_select_product').on('change', function() {
        var selectedValue = $(this).val();
        if (!selectedValue) return;

        $.ajax({
            url: ajaxurl, // WordPress variable for admin AJAX URL
            method: 'POST',
            data: {
                action: 'wbgs_get_product_meta',
                product_id: selectedValue
            },
            success: function(response) {
                if (response.success) {
                    $('#wbgs_duration').val(response.data.wbgs_duration);
                    $('#wbgs_stock_alert').val(response.data.wbgs_stock_alert);
                    $('#wbgs_banner_image').val(response.data.wbgs_banner_image);
                } else {
                    alert('Failed to fetch product metadata.');
                }
            },
            error: function() {
                alert('AJAX request failed.');
            }
        });
    });
});