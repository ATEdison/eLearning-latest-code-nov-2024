(function ($) {
  $(document).ready(function () {
    if ($('.sta-user-details').length < 1) {
      return;
    }

    $(document).on('click', '.sta-user-details-btn-edit', function () {
      $(this).closest('.sta-user-details').toggleClass('form-active');
    });
  });
})(jQuery);
