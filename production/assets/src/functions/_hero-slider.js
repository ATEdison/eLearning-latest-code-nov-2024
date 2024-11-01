(function ($) {
  $(document).ready(function () {
    var $sliderHolder = $('.sta-hero-slider');
    if ($sliderHolder.length < 1) {
      return;
    }

    initSliders();
    $(window).resize(function () {
      initSliders();
    });

    $(document).on('click', '.sta-hero-slider-navigation-item button', function () {
      var $navItem = $(this).parent();
      if ($navItem.hasClass('active')) {
        return;
      }

      var $navigationSlider = $navItem.closest('.sta-hero-slider-navigation');
      if ($navigationSlider.hasClass('slick-initialized')) {
        $navigationSlider.slick('slickGoTo', $navItem.attr('data-slick-index'));
      }

      $navItem.parent().find('> .active').removeClass('active');
      $navItem.addClass('active');
      $navItem.closest('.sta-hero-slider').find('.sta-hero-slider-slider').slick('slickGoTo', $navItem.index());
    });

    $(document).on('beforeChange', '.sta-hero-slider-navigation', function (e, slick, currentSlide, nextSlide) {
      // console.log({ e, slick, currentSlide });
      // window.staSlick = slick;
      $(slick.$slides[nextSlide]).find('button').trigger('click');
    });

    function initSliders() {
      $sliderHolder.each(function () {
        var $slider = $(this).find('.sta-hero-slider-slider');
        var $navigation = $(this).find('.sta-hero-slider-navigation');

        if (!$slider.hasClass('slick-initialized')) {
          $slider.slick({
            infinite: false,
            arrows: false,
            dots: false,
            mobileFirst: true,
            touchMove: false,
            swipe: false,
            fade: true,
            cssEase: 'linear'
          });
        }

        if (!$navigation.hasClass('slick-initialized')) {
          $navigation.slick({
            infinite: false,
            arrows: true,
            dots: false,
            mobileFirst: true,
            touchMove: false,
            swipe: false,
            centerMode: true,
            variableWidth: true,
            // fade: true,
            // cssEase: 'linear',
            responsive: [
              {
                breakpoint: 992,
                settings: 'unslick',
              },
            ]
          });
        }
      });
    }
  });
})(jQuery);
