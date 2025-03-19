jQuery(document).ready(function ($) {
    // Initialize Fancybox for gallery images
    $('[data-fancybox="ig-gallery"]').fancybox({
        // Enable mouse scroll and drag
        wheel: true, // Enable mouse wheel navigation
        arrows: true, // Show navigation arrows
        infobar: true, // Show info bar
        buttons: [
            "zoom",
            "close"
        ],
        animationEffect: "fade", // Transition effect
        transitionEffect: "slide", // Slide effect for next/prev
        preventCaptionOverlap: true, // Prevent caption overlap
        idleTime: false, // Disable idle time (no auto-close)
        gutter: 50, // Space between images
        keyboard: true, // Enable keyboard navigation
        toolbar: true, // Show toolbar
        loop: true, // Loop through images
        clickContent: false, // Disable click-to-next on the image
        clickSlide: "close", // Click outside the image to close the lightbox
        dblclickContent: "zoom", // Double-click to zoom
        dblclickSlide: "zoom", // Double-click slide to zoom
        touch: {
            vertical: false, // Disable vertical touch/swipe
            momentum: false, // Disable momentum scrolling
        },
        slideShow: {
            autoStart: false, // Don't auto-start slideshow
            speed: 3000, // Slideshow speed in milliseconds
        },
        protect: true, // Prevent images from being stretched
        afterLoad: function (instance, current) {
            // Ensure the image fits within the viewport
            current.$image.css({
                'max-width': '100%',
                'max-height': '100%',
                'width': 'auto',
                'height': 'auto',
            });
        },
        onInit: function (instance) {
            // Enable drag-to-navigate in both directions
            var isDragging = false;
            var startX = 0;

            instance.$refs.stage.on('mousedown', function (e) {
                isDragging = true;
                startX = e.pageX;
            });

            $(document).on('mousemove.drag', function (e) {
                if (isDragging) {
                    var diffX = e.pageX - startX;

                    // Trigger navigation based on horizontal drag
                    if (diffX > 50) {
                        instance.prev(); // Drag right to go to previous image
                        isDragging = false;
                        $(document).off('mousemove.drag');
                    } else if (diffX < -50) {
                        instance.next(); // Drag left to go to next image
                        isDragging = false;
                        $(document).off('mousemove.drag');
                    }
                }
            });

            $(document).on('mouseup', function () {
                isDragging = false;
                $(document).off('mousemove.drag');
            });
        },
    });
});