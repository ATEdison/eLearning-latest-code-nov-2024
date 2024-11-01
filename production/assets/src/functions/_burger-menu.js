(function ($) {
  $(document).ready(function () {
    // test
    // toggleBurgerMenu();
    // setTimeout(function () {
    //   $('.btn-toggle-user-menu-dropdown').trigger('click');
    // }, 10);

    $(document).on('click', '.btn-sidebar', function () {
      document.body.classList.add('sidebar-menu-active');
    });

    $(document).on('click', '.btn-close-sidebar', function () {
      document.body.classList.remove('sidebar-menu-active');
    });

    $(document).on('click', '.btn-toggle-burger-menu', function () {
      toggleBurgerMenu();
    });

    function toggleBurgerMenu() {
      document.body.classList.toggle('burger-menu-active');
    }
  });
})(jQuery);
