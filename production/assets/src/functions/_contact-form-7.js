(function ($) {
  $(document).ready(function () {
    var $forms = $('.wpcf7-form');
    if ($forms.length < 1) {
      return;
    }

    $forms.find('select').each(function () {
      $(this).find('option[value=""]').text(staSettings.i18n.selectPlaceholder);
      initSelectEmpty($(this));
    });

    $(document).on('change', '.wpcf7-form select', function () {
      initSelectEmpty($(this));
    });

    function initSelectEmpty($select) {
      if (!$select.val()) {
        $select.addClass('empty');
      } else {
        $select.removeClass('empty');
      }
    }
  });
})(jQuery);
