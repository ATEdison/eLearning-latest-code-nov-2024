(function ($) {
  var paddingTop = 150;

  $(document).on('click', '.nn-anchor', function (e) {
    e.preventDefault();
    var target = $(this).attr('href');
    var $target = $(target);
    animateTo($target);
  });

  window.staHelpers = typeof window.staHelpers !== 'undefined' ? window.staHelpers : {};

  window.staHelpers.animateTo = animateTo;

  function animateTo($target) {
    if ($target.length < 1) {
      return;
    }
    while ($target && $target.is(':hidden')) {
      $target = $target.parent();
    }

    if (!$target || $target.length < 1) {
      return;
    }

    var targetOffset = $target.offset().top - paddingTop;
    $([document.documentElement, document.body]).animate({ scrollTop: targetOffset }, 500);
  }
})(jQuery);
