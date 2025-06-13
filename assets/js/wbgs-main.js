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