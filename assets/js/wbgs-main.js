 // Used for upload banner image
 jQuery(document).ready(function($) {
    let mediaUploader; // Declare outside the click handler

    $('#wbgs_upload_banner').on('click', function(e) {
        e.preventDefault();

        // If mediaUploader already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create a new media uploader instance
        mediaUploader = wp.media({
            title: 'Select Banner',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // When an image is selected, run this function
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();

            // Set the URL in the hidden input field
            $('#wbgs_modal_banner').val(attachment.url);

            // Show the image preview
            $('#wbgs_banner_preview').html(
                '<img src="' + attachment.url + '" id="wbgs-hide-image" style="max-width:100px;">'
            );
        });

        // Open the media uploader
        mediaUploader.open();
    });

// add or update hidden values using ajax
$('#wbgs_save_modal').on('click', function(e) {
        e.preventDefault();
       let datetime = $('#wbgs_modal_end_time').val();
       let dateOnly = datetime.replace('T', ' ');
       $('#wbgs_modal_end_time').val(datetime);
        const data = {
            action: 'wbgs_save_product_settings',
            product_id: $('#wbgs_modal_product_id').val(),
            stock_alert: $('#wbgs_modal_stock_alert').val(),
            end_time: dateOnly,
            banner_image: $('#wbgs_modal_banner').val(),
            disable:'disable',
            wbgs_ajax_nonce: wbgs_data.nonce || false
        };
        $.ajax({
            url: wbgs_data.ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success === true) {
                $('#wbgs_message').addClass('notice-success')
                .css('display','block')
                .removeClass('notice-error')
                .text(response.data.message)
                .fadeIn();

                // Clear inputs after success
                $('#wbgs_modal_product_id').val('');
                $('#wbgs_modal_stock_alert').val('');
                $('#wbgs_modal_end_time').val('');
                $('#wbgs_modal_banner').val('');
                $('#wbgs_banner_preview').empty();

                messageTimer = setTimeout(function() {
                    $('#wbgs_message').fadeOut();
                }, 10000);
                 $('#wbgs_products_table').load(location.href + ' #wbgs_products_table > *');
                } else {
                $('#wbgs_message')
                .addClass('notice-error')
                .removeClass('notice-success')
                .text((response.data && response.data.message) || 'Unknown error')
                .fadeIn();
            // Clear inputs after success
            $('#wbgs_modal_product_id').val('');
            $('#wbgs_modal_stock_alert').val('');
            $('#wbgs_modal_end_time').val('');
            $('#wbgs_modal_banner').val('');
            $('#wbgs_banner_preview').empty();
            messageTimer = setTimeout(function() {
                $('#wbgs_message').fadeOut();
            }, 10000);

             $('#wbgs_products_table').load(location.href + ' #wbgs_products_table > *');
            }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    });
//Update radio button status
$(document).on('change', 'input[name="choice"]', function () {
    var $selected = $(this);
    var selectedProductId = $selected.data('product-id');

    // Get all product IDs
    var allProductIds = $('input[name="choice"]').map(function () {
        return $(this).data('product-id');
    }).get();

    $.ajax({
        url: wbgs_data.ajaxurl,
        type: 'POST',
        data: {
            action: 'wbgs_edit_product_status',
            selected_product_id: selectedProductId,
            all_product_ids: allProductIds
        },
        success: function (response) {
            console.log('Statuses updated:', response);
            $('#wbgs_products_table').load(location.href + ' #wbgs_products_table > *');
        },
        error: function (xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
});

});