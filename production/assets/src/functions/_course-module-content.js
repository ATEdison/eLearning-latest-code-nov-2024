(function ($) {
  $(document).ready(function () {
    if ($('.course-module-step-item').length < 1) {
      return;
    }

    // expand / collapse
    $(document).on('click', '.course-module-step-item-heading', function () {
      var $holder = $(this).parent();
      if ($holder.hasClass('expanded')) {
        collapseModuleStep($holder);
      } else {
        expandModuleStep($holder);
      }
    });

    // btn-expand-all
    $(document).on('click', '.sta-course-module-content .btn-expand-all', function () {
      $(this).toggleClass('expanded');

      if ($(this).hasClass('expanded')) {
        $('.course-module-step-item').each(function () {
          expandModuleStep($(this));
        });
      } else {
        $('.course-module-step-item').each(function () {
          collapseModuleStep($(this));
        });
      }
    });

    function expandModuleStep($holder) {
      var $content = $holder.find('> .course-module-step-item-content');
      var contentHeight = $content.find('> div').outerHeight();
      $content.css('height', contentHeight + 'px');
      $holder.addClass('expanded');
    }

    function collapseModuleStep($holder) {
      var $content = $holder.find('> .course-module-step-item-content');
      $content.css('height', '');
      $holder.removeClass('expanded');
    }
  });
})(jQuery);
