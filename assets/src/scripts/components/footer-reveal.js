(function(jQuery) {
    jQuery.fn.parallaxFooter = function(options) {
        var $this = jQuery(this),
            $prev = $this.prev(),
            $win = jQuery(window),
            defaults = jQuery.extend(
                {
                    zIndex: -100
                },
                options
            );
        if ($this.outerHeight() <= $win.outerHeight() && $this.offset().top >= $win.outerHeight()) {
            $this.css({
                'z-index': defaults.zIndex,
                position: 'fixed',
                bottom: 0
            });

            $win.on('load resize parallaxFooterResize', function() {
                $this.css({
                    width: $prev.outerWidth()
                });
                $prev.css({
                    'margin-bottom': $this.outerHeight()
                });
            });
        }

        return this;
    };
})(jQuery);

jQuery(function() {
    jQuery('.site-footer').parallaxFooter();

    jQuery(window).scroll(function() {
        var $window = jQuery(window).height();
        if (jQuery(this).scrollTop() >= $window) {
            jQuery('.block-cover').addClass('is-hidden');
        } else {
            jQuery('.block-cover').removeClass('is-hidden');
        }
    });
});
