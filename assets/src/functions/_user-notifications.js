(function ($) {
  $(document).ready(function () {
    $(document).on('click', '.sta-notification-list-item-read-more', function () {
      var $notification = $(this).closest('.sta-notification-list-item');
      $notification.toggleClass('active');
      if ($notification.hasClass('new')) {
        $notification.removeClass('new');
        markAsRead($notification);
      }
    });

    function markAsRead($notification) {
      var notificationId = $notification.attr('data-id');
      $.ajax({
        url: staSettings.ajaxUrl,
        type: 'POST',
        data: {
          action: 'sta_notification_mark_as_read',
          notification_id: notificationId,
        },
        success: function (response) {
          if (response.success) {
            var notificationCount = response.data.count;
            var $notificationCount = $('.btn-user-dashboard span');
            if (notificationCount < 1) {
              $notificationCount.remove();
            } else {
              $notificationCount.text(notificationCount);
            }
          }
        }
      });
    }
  });
})(jQuery);
