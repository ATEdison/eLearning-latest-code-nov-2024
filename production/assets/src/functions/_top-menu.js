(function ($) {
  $(document).ready(function () {
    var $topMenuToggler = $('.btn-toggle-header-top-menu');

    $(document).on('click', '.btn-toggle-header-top-menu', function () {
      $(this).next().toggleClass('active');
    });

    onInit();

    function onInit() {
      var activeValue = $('.header-top-menu > ul > li.current-menu-item > a').text();
      if (!activeValue) {
        activeValue = $('.header-top-menu > ul > li.menu-item-home > a').text();
      }
      $topMenuToggler.text(activeValue);
    }
  });
})(jQuery);
