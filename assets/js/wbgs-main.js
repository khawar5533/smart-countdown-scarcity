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
    $('#wbgs_modal_end_time').val(datetime); // Preserve original

    const editId = $('#wbgs_edit_id').val()?.trim();
    const isEditing = editId !== '' && editId !== '0';

    const data = {
        action: 'wbgs_save_product_settings',
        product_id: $('#wbgs_modal_product_id').val(),
        title: $('#wbgs_item_title').val(),
        subtitle: $('#wbgs_item_subtitle').val(),
        flashsaletitle:$('#wbgs_sale_title').val(),
        discounttitle:$('#wbgs_banner_discount').val(),
        discountoff:$('#wbgs_banner_percent').val(),
        description: $('#wbgs_item_description').val(),
        stock_alert: $('#wbgs_modal_stock_alert').val(),
        end_time: dateOnly,
        banner_image: $('#wbgs_modal_banner').val(),
        template: $('#wbgs_template_select').val(),
        disable: 'disable',
        edit_id: editId,
        wbgs_ajax_nonce: wbgs_data.nonce || false
    };

    $.ajax({
        url: wbgs_data.ajaxurl,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            let $message = $('#wbgs_message');
            let isSuccess = response.success === true;
            let msgText = (response.data && response.data.message) || 'Unknown response';

            if (isSuccess && response.data.shortcode) {
                msgText += `<br><strong>Shortcode:</strong> <code>${response.data.shortcode}</code>`;
            }

            $message
                .removeClass('notice-error notice-success')
                .addClass(isSuccess ? 'notice-success' : 'notice-error')
                .html(msgText)
                .fadeIn();

            clearTimeout(window.messageTimer);
            window.messageTimer = setTimeout(() => {
                $message.fadeOut();
            }, 10000);

            if (isEditing) {
                location.reload();
            } else {
                $('#wbgs_modal_product_id').val('');
                $('#wbgs_modal_stock_alert').val('');
                $('#wbgs_modal_end_time').val('');
                $('#wbgs_modal_banner').val('');
                $('#wbgs_template_select').val('');
                $('#wbgs_item_title').val('');
                $('#wbgs_item_subtitle').val('');
                $('#wbgs_item_description').val('');
                $('#wbgs_sale_title').val(''),
                $('#wbgs_banner_discount').val(''),
                $('#wbgs_banner_percent').val(''),
                $('#wbgs_banner_preview').empty();

                $('#wbgs_products_table').load(location.href + ' #wbgs_products_table > *');
            }
        },
        error: function(xhr, status, error) {
            alert('AJAX Error: ' + error);
        }
    });
});

// Update banner recored
$('.wbgs-edit-button').on('click', function(e) {
    e.preventDefault();

    let $btn = $(this);
    let timestamp = parseInt($btn.data('end_time'));
    let formattedDate = '';

    if (!isNaN(timestamp)) {
        let date = new Date(timestamp * 1000); // Convert to milliseconds
        let year = date.getUTCFullYear();
        let month = ('0' + (date.getUTCMonth() + 1)).slice(-2);
        let day = ('0' + date.getUTCDate()).slice(-2);
        let hours = ('0' + date.getUTCHours()).slice(-2);       // ✅ 24-hour format
        let minutes = ('0' + date.getUTCMinutes()).slice(-2);
        let seconds = ('0' + date.getUTCSeconds()).slice(-2);

        formattedDate = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
    }

    // Populate form fields
    $('#wbgs_edit_id').val($btn.data('product_id'));
    $('#wbgs_modal_product_id').val($btn.data('product_id')).prop('disabled', true); // Disable select
    $('#wbgs_template_select').val($btn.data('template'));
    $('#wbgs_modal_stock_alert').val($btn.data('stock'));
    $('#wbgs_modal_end_time').val(formattedDate);
    $('#wbgs_modal_banner').val($btn.data('banner'));
    $('#wbgs_item_title').val($btn.data('title'));
    $('#wbgs_item_subtitle ').val($btn.data('subtitle'));
    $('#wbgs_item_description').val($btn.data('description'));
    $('#wbgs_banner_discount').val($btn.data('discounttitl'));
    $('#wbgs_sale_title').val($btn.data('flashsale'));
    $('#wbgs_banner_percent').val($btn.data('discounttxt'));
    


    let bannerUrl = $btn.data('banner');
    if (bannerUrl) {
        $('#wbgs_banner_preview').html('<img src="' + bannerUrl + '" style="max-width: 100px;">');
    } else {
        $('#wbgs_banner_preview').empty();
    }
});


//Update radio button status
$(document).on('change', 'input[name="choice"]', function () {
    var $selected = $(this);
    var selectedProductId = $selected.data('product-id');

    var allProductIds = $('input[name="choice"]').map(function () {
        return $(this).data('product-id');
    }).get();

    var loadingTimeout = setTimeout(function () {
        $('#wbgs_loadingDiv').show();
    }, 300);

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
            clearTimeout(loadingTimeout);

            // Reload the table with a callback to hide loading AFTER load completes
            $('#wbgs_products_table').load(location.href + ' #wbgs_products_table > *', function () {
                $('#wbgs_loadingDiv').hide();
            });
        },
        error: function (xhr, status, error) {
            console.error('AJAX error:', error);
            clearTimeout(loadingTimeout);
            $('#wbgs_loadingDiv').hide();
        }
    });
  });

});

