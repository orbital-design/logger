function getCookie() {
    var cookieName = 'userChoice';
    var v = document.cookie.match('(^|;) ?' + cookieName + '=([^;]*)(;|$)');
    if (v !== null) {
        jQuery('.uco-wrapper').css('display', 'none');
    } else {
        jQuery('.uco-wrapper').css('display', 'block');
    }
}
jQuery(document).ready(function($) {
    $('.uco-modal__button').on('click', function() {
        // console.log('button clicked');
        var d = new Date();
        var choice = $(this).data('choice');
        var cookieName = 'userChoice';
        d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * 365);
        document.cookie = cookieName + '=' + choice + ';path=/;expires=' + d.toGMTString();

        $('.uco-wrapper').fadeOut('slow');
    });
});

jQuery(window).load(function() {
    getCookie();
});

jQuery(document).ready(function() {
    var fadeVerticalSliderOptions = {
        slidesToShow: 1,
        slidesToScroll: 1,
        easing: 'swing', // see http://api.jquery.com/animate/
        speed: 700,
        dots: true,
        fade: true,
        arrows: false,
        appendDots: jQuery('.block-vslider__controls'),
        customPaging: function(slick, index) {
            return '<button type="button" data-role="none" data-slick-index="' + index + '">' + index + '</button>';
        }
    };

    var slideVerticalSliderOptions = {
        slidesToShow: 1,
        slidesToScroll: 1,
        easing: 'swing', // see http://api.jquery.com/animate/
        speed: 700,
        dots: true,
        vertical: true,
        // fade: true,
        arrows: false,
        // appendArrows: jQuery('.block-vslider__controls'),
        appendDots: jQuery('.block-vslider__controls'),
        asNavFor: '.block-vslider__bg-slider',
        // nextArrow: '<button class="block-vslider__arrow block-vslider__arrow--next" aria-label="Next" type="button"><i class="fal fa-chevron-down"></i></button>',
        // prevArrow: '<button class="block-vslider__arrow block-vslider__arrow--prev" aria-label="Previous" type="button"><i class="fal fa-chevron-up"></i></button>',
        customPaging: function(slick, index) {
            return '<button type="button" data-role="none" data-slick-index="' + index + '">' + index + '</button>';
        }
    };

    var slideVerticalBackgroundSliderOptions = {
        slidesToShow: 1,
        slidesToScroll: 1,
        easing: 'swing',
        speed: 700,
        dots: false,
        vertical: true,
        arrows: false
    };

    // Init slick carousel
    jQuery('.block-vslider__slider--fade').slick(fadeVerticalSliderOptions);
    jQuery('.block-vslider__slider--slide').slick(slideVerticalSliderOptions);
    jQuery('.block-vslider__bg-slider').slick(slideVerticalBackgroundSliderOptions);

    jQuery('.slick-dots > li').each(function() {
        var pagerItem = jQuery(this),
            slickIndex = pagerItem.find('button').data('slick-index'),
            matchingSlide = jQuery('.block-vslider__slide[data-slick-index="' + slickIndex + '"]'),
            titleContent = matchingSlide.data('pagination');

        pagerItem.find('button').text(titleContent);
    });
});

const dynamicOnEntry = dynamicSequence('.animate', 'ani--sh');

var dynamicObserver = new IntersectionObserver(dynamicOnEntry),
    allDynamicElements = document.querySelectorAll('.animate');

allDynamicElements.forEach(e => dynamicObserver.observe(e));

function dynamicSequence(targetSelector, classToAdd, delay = 50) {
    const animatedElements = [];
    let chain = Promise.resolve();

    function show(e) {
        return new Promise((res, rej) => {
            setTimeout(() => {
                e.classList.add(classToAdd);
                res();
            }, delay);
        });
    }
    return function(entries) {
        entries.forEach(entry => {
            if (entry.intersectionRatio > 0) {
                const elem = entry.target;
                if (!animatedElements.includes(elem)) {
                    animatedElements.push(elem);
                    chain = chain.then(() => show(elem));
                    dynamicObserver.unobserve(entry.target);
                }
            }
        });
    };
}
