(function ($) {
  $(document).ready(function () {
    var $filter = $('.course-grid-filter');
    if ($filter.length < 1) {
      return;
    }
    $(document).on('change', '.course-grid-filter-category input', function () {
      doFilter();
    });

    function doFilter() {
      var activeCategory = $('.course-grid-filter-category input:checked').val();
      $('.course-preview').each(function () {
        var $item = $(this);
        var $itemHolder = $item.parent();

        // show all
        if (activeCategory === '__all__') {
          $itemHolder.removeClass('d-none');
          return;
        }

        // show courses in a specific category only
        var dataCategories = $item.attr('data-categories');
        var courseCategories = dataCategories ? JSON.parse($item.attr('data-categories')) : [];
        if (courseCategories.includes(activeCategory)) {
          $itemHolder.removeClass('d-none');
        } else {
          $itemHolder.addClass('d-none');
        }
      });
    }
  });
})(jQuery);
