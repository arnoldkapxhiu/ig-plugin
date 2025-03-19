jQuery(document).ready(function($) {
    // Initialize jQuery UI Sortable
    $('.ig-gallery-images').sortable({
        update: function(event, ui) {
            // Update the hidden input with the new order of attachment IDs
            var galleryIds = [];
            $('.ig-gallery-images .ig-gallery-image').each(function() {
                galleryIds.push($(this).data('attachment-id'));
            });
            $('#ig_gallery_images').val(galleryIds.join(','));
        }
    });

    // Upload images
    $('.ig-upload-gallery-images').on('click', function(e) {
        e.preventDefault();

        // Initialize the WordPress media uploader
        var frame = wp.media({
            title: 'Select Gallery Images',
            multiple: true,
            library: { type: 'image' },
            button: { text: 'Add to Gallery' }
        });

        // When images are selected
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var galleryIds = $('#ig_gallery_images').val().split(',').filter(Boolean);

            // Loop through selected attachments
            attachments.forEach(function(attachment) {
                if (attachment.id && attachment.sizes && attachment.sizes.thumbnail) {
                    galleryIds.push(attachment.id);
                    $('.ig-gallery-images').append(
                        '<li class="ig-gallery-image" data-attachment-id="' + attachment.id + '">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.title + '">' +
                        '<a href="#" class="ig-remove-image">Remove</a>' +
                        '</li>'
                    );
                }
            });

            // Update the hidden input with the new gallery IDs
            $('#ig_gallery_images').val(galleryIds.join(','));
        });

        // Open the media uploader
        frame.open();
    });

    // Remove images
    $('.ig-gallery-images').on('click', '.ig-remove-image', function(e) {
        e.preventDefault();
        var attachmentId = $(this).closest('.ig-gallery-image').data('attachment-id');
        var galleryIds = $('#ig_gallery_images').val().split(',').filter(Boolean);
        galleryIds = galleryIds.filter(function(id) {
            return id != attachmentId;
        });
        $('#ig_gallery_images').val(galleryIds.join(','));
        $(this).closest('.ig-gallery-image').remove();
    });
});

jQuery(document).ready(function($) {
    // Initialize jQuery UI Sortable
    $('.ig-gallery-images').sortable({
        update: function(event, ui) {
            // Update the hidden input with the new order of attachment IDs
            var galleryIds = [];
            $('.ig-gallery-images .ig-gallery-image').each(function() {
                galleryIds.push($(this).data('attachment-id'));
            });
            $('#ig_gallery_images').val(galleryIds.join(','));
        }
    });

    // Upload images
    $('.ig-upload-gallery-images').on('click', function(e) {
        e.preventDefault();

        // Initialize the WordPress media uploader
        var frame = wp.media({
            title: 'Select Gallery Images',
            multiple: true,
            library: { type: 'image' },
            button: { text: 'Add to Gallery' }
        });

        // When images are selected
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var galleryIds = $('#ig_gallery_images').val().split(',').filter(Boolean);

            // Loop through selected attachments
            attachments.forEach(function(attachment) {
                if (attachment.id && attachment.sizes && attachment.sizes.thumbnail) {
                    galleryIds.push(attachment.id);
                    $('.ig-gallery-images').append(
                        '<li class="ig-gallery-image" data-attachment-id="' + attachment.id + '">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.title + '">' +
                        '<a href="#" class="ig-remove-image">Remove</a>' +
                        '</li>'
                    );
                }
            });

            // Update the hidden input with the new gallery IDs
            $('#ig_gallery_images').val(galleryIds.join(','));
        });

        // Open the media uploader
        frame.open();
    });

    // Remove images
    $('.ig-gallery-images').on('click', '.ig-remove-image', function(e) {
        e.preventDefault();
        var attachmentId = $(this).closest('.ig-gallery-image').data('attachment-id');
        var galleryIds = $('#ig_gallery_images').val().split(',').filter(Boolean);
        galleryIds = galleryIds.filter(function(id) {
            return id != attachmentId;
        });
        $('#ig_gallery_images').val(galleryIds.join(','));
        $(this).closest('.ig-gallery-image').remove();
    });
});