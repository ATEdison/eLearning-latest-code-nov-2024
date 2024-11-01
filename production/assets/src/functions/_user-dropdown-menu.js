(function ($) {
  $(document).ready(function () {
    var $userDropdownMenu = $('.user-menu-dropdown');
    if ($userDropdownMenu.length < 1) {
      return;
    }

    // mobile
    $(document).on('click', '.btn-toggle-user-menu-dropdown', function () {
      $(this).toggleClass('active');
    });

    $(document).on('click', '.btn-dropdown-back', function () {
      $('.btn-toggle-user-menu-dropdown').toggleClass('active');
    });
  });
})(jQuery);
