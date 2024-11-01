(function ($) {
  $(document).ready(function () {
    var $menu = $('.sta-language-selector-list');
    if ($menu.length < 1) {
      return;
    }

    var mouseEntered = false;

    $(document).on('mouseenter', '.sta-language-selector-current-language, .sta-language-selector-list', function () {
      var windowWidth = $(window).width();

      // desktop only
      if (windowWidth >= 992) {
        var menuHeight = $menu.find('ul').outerHeight();
        $menu.css('height', menuHeight + 20 + 'px').css('padding-top', '20px');
        mouseEntered = true;
      }
    });

    $(document).on('mouseleave', '.sta-language-selector-list', function () {
      var windowWidth = $(window).width();

      // desktop only
      if (windowWidth >= 992) {
        mouseEntered = false;
        setTimeout(function () {
          if (!mouseEntered) {
            $menu.css('height', '').css('padding-top', '');
          }
        }, 300);
      }
    });

    $(window).resize(function () {
      $menu.css('height', '').css('padding-top', '');
    });
  });
})(jQuery);
