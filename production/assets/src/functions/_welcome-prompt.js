(function ($) {
  $(document).ready(function () {
    var $welcomePrompt = $('.sta-welcome-prompt');
    if ($welcomePrompt.length < 1) {
      return;
    }

    var $slider = $('.sta-welcome-prompt-step-list');

    $slider.slick({
      dots: true,
      arrows: false,
      // infinite: true,
      speed: 500,
      fade: true,
      cssEase: 'linear',
      appendDots: $('.sta-welcome-prompt-step-list-navigation'),
    });

    $(document).on('beforeChange', '.sta-welcome-prompt-step-list', function (event, slick, currentSlide, nextSlide) {
      if (nextSlide === slick.$slides.length - 1) {
        $welcomePrompt.addClass('final-step');
      } else {
        $welcomePrompt.removeClass('final-step');
      }

      // console.log(nextSlide);
      var $video = $(slick.$slides[nextSlide]).find('.sta-welcome-prompt-video');
      if ($video.find('iframe').length < 1) {
        var videoUrl = $video.attr('data-src') + '?autoplay=1&enablejsapi=1&version=3&playerapiid=ytplayer';
        $video.append('<iframe src="' + videoUrl + '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
      }

      pauseVideo($(slick.$slides[currentSlide]).find('iframe'));
      playVideo($video.find('iframe'));
    });

    // next
    $(document).on('click', '.btn-sta-welcome-prompt-next', function () {
      // next
      if (!$welcomePrompt.hasClass('final-step')) {
        $slider.slick('slickNext');
        return;
      }

      // finish
      closePrompt();
    });

    // skip
    $(document).on('click', '.btn-sta-welcome-prompt-skip', function () {
      closePrompt();
    });

    function closePrompt() {
      var shouldClose = confirm('Close prompt?');
      if (!shouldClose) {
        return;
      }

      document.body.classList.remove('sta-welcome-prompt-active');
      $welcomePrompt.remove();

      $.ajax({
        url: staSettings.ajaxUrl,
        type: 'POST',
        data: {
          action: 'sta_finish_welcome_prompt'
        },
        success: function (response) {
        }
      });
    }

    function playVideo($iframe) {
      if ($iframe.length < 1) {
        return;
      }
      $iframe[0].contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
    }

    function pauseVideo($iframe) {
      if ($iframe.length < 1) {
        return;
      }
      $iframe[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
    }
  });
})(jQuery);
