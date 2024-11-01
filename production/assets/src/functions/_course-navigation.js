(function ($) {
  $(document).ready(function () {
    if ($('.sta-course-navigation').length < 1) {
      return;
    }

    $(document).on('click', '.btn-expand-toc-lesson', function () {
      var $holder = $(this).closest('.sta-course-navigation-toc-lesson');
      if ($holder.hasClass('expanded')) {
        collapseTocLesson($holder);
      } else {
        expandTocLesson($holder);
      }
    });

    $(document).on('click', '.btn-toggle-course-navigation', function () {
      document.body.classList.toggle('course-navigation-active');
    });

    onInit();

    function onInit() {
      $('.sta-course-navigation-toc-lesson.expanded').each(function () {
        expandTocLesson($(this));
      });
    }

    function expandTocLesson($holder) {
      var $steps = $holder.find('.sta-course-navigation-toc-lesson-steps');
      var $stepHeight = $steps.find('> ul').outerHeight();
      $steps.css('height', $stepHeight + 'px');
      $holder.addClass('expanded');
    }

    function collapseTocLesson($holder) {
      var $steps = $holder.find('.sta-course-navigation-toc-lesson-steps');
      $steps.css('height', '');
      $holder.removeClass('expanded');
    }
  });
})(jQuery);
