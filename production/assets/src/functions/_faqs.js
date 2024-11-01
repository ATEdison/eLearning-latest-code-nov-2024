(function ($) {
  $(document).ready(function () {
    var $faqsFilter = $('.sta-faqs-filter');
    if ($faqsFilter.length < 1) {
      return;
    }

    $(document).on('change', '.sta-faqs-filter input', function () {
      doFilter($(this).closest('.sta-faqs-filter'));
    });

    function doFilter($filterHolder) {
      var activeCategory = $filterHolder.find('input:checked').val();
      var isFirst = true;

      $filterHolder.closest('.sta-faqs').find('.accordion-item').each(function () {
        var $item = $(this);

        // filter all
        if (activeCategory === 'all') {
          $item.removeClass('d-none');
          if (isFirst) {
            $item.find('.accordion-button').trigger('click');
          }
          isFirst = false;
          return;
        }

        // filter specific category
        var itemCategoriesString = $item.attr('data-categories');
        var itemCategories = itemCategoriesString ? JSON.parse(itemCategoriesString) : [];
        if (itemCategories.includes(activeCategory)) {
          $item.removeClass('d-none');
          if (isFirst) {
            $item.find('.accordion-button').trigger('click');
          }
          isFirst = false;
        } else {
          $item.addClass('d-none');
        }
      });
    }
  });
})(jQuery);
