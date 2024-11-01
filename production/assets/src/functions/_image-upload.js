(function ($) {
  $(document).ready(function () {
    if ($('.sta-image-upload').length < 1) {
      return;
    }

    $(document).on('change', '.sta-image-upload input[type="file"]', function () {
      var $holder = $(this).closest('.sta-image-upload');
      var $previewNew = $holder.find('.sta-image-upload-preview-new');

      var image = this.files.length > 0 ? this.files[0] : null;
      if (!image) {
        $previewNew.empty();
      } else {
        $previewNew.html('<img src="' + URL.createObjectURL(image) + '"/>');
      }
    });

    $(document).on('click', '.sta-image-upload .btn-upload-image', function () {
      $(this).closest('.sta-image-upload').find('input[type="file"]').trigger('click');
    });

    $(document).on('click', '.sta-image-upload .btn-trash', function () {
      var $holder = $(this).closest('.sta-image-upload');
      var $previewNew = $holder.find('.sta-image-upload-preview-new');
      $previewNew.empty();
    });
  });
})(jQuery);
