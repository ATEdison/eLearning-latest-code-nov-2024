(function ($) {
  $(document).ready(function () {
    var $audio = $('.sta-audio');
    if ($audio.length < 1) {
      return;
    }

    var audioList = {};

    $(document).on('click', '.sta-audio', function (e) {
      e.preventDefault();
      var $btn = $(this);
      $btn.toggleClass('playing');
      var src = $(this).attr('data-src');

      if (!audioList[src]) {
        audioList[src] = new Audio(src);

        // on ended
        audioList[src].onended = function () {
          $btn.removeClass('playing');
        }
      }

      if ($btn.hasClass('playing')) {
        audioList[src].play();
      } else {
        audioList[src].pause();
      }
    });
  });
})(jQuery);
