(function ($) {
  $(document).ready(function () {
    if ($('.sta-leaderboard').length < 1) {
      return;
    }

    var ajaxXhr = null;

    var $loadMore = $('.sta-leaderboard-load-more');
    var $data = $('.sta-leaderboard-data');
    var $filterCountry = $('.leaderboard-filter-country');
    var $countrySelector = $('#leaderboard_filter_region');

    var templateLeaderboardItem = wp.template('sta-leader-board-item');

    // load more
    $(document).on('click', '.sta-leaderboard-load-more button', function () {
      if ($loadMore.hasClass('loading')) {
        return;
      }
      $loadMore.addClass('loading');
      ajaxLeaderboardLoadMore(true);
    });

    // filter by country
    $(document).on('change', '#leaderboard_filter_region', function () {
      $filterCountry.addClass('loading');
      ajaxLeaderboardLoadMore(false);
    });

    function ajaxLeaderboardLoadMore(append) {
      // abort previous ajax
      if (ajaxXhr) {
        ajaxXhr.abort();
        ajaxXhr = null;
      }

      var offset = append ? $('.sta-leaderboard-item').length : 0;
      var country = $countrySelector.val();

      ajaxXhr = $.ajax({
        url: staSettings.ajaxUrl,
        type: 'POST',
        data: {
          action: 'sta_leaderboard_load_more',
          offset: offset,
          country: country,
        },
        success: function (response) {
          var newContent = '';
          var hasMore = response.success && response.has_more;

          if (response.success) {
            $.each(response.data, function (itemIndex, item) {
              newContent += templateLeaderboardItem(item);
            });

            // append
            if (append) {
              $data.append(newContent);
            } else {
              // replace
              $data.html(newContent);
            }
          }

          if (hasMore) {
            $loadMore.removeClass('d-none');
          } else {
            $loadMore.addClass('d-none');
          }
        },
        complete: function () {
          $loadMore.removeClass('loading');
          $filterCountry.removeClass('loading');
        }
      });
    }
  });
})(jQuery);
