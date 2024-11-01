(function ($) {
  $.validator.addMethod('passwordCheck', function (value) {
    // Be at least 6 characters long
    if (!window.staHelpers.minLength(value, 6)) {
      return false;
    }

    // Contain at least one lowercase letter
    if (!window.staHelpers.containLowercase(value)) {
      return false;
    }

    // Contain at least one uppercase letter
    if (!window.staHelpers.containUppercase(value)) {
      return false;
    }

    // Contain at least one digit
    if (!window.staHelpers.containDigit(value)) {
      return false;
    }

    // One special character ~!@#$%^&*()_+
    if (!window.staHelpers.containSpecialCharacter(value)) {
      return false;
    }

    return true;
  }, function (value, element) {
    return false;
  });

  $.validator.addMethod('fileSizeMax', function (value, element, param) {
    var isOptional = this.optional(element);
    if (isOptional) {
      return isOptional;
    }
    if ($(element).attr('type') === 'file') {
      if (element.files && element.files.length) {
        var file = element.files[0];
        return (file.size && file.size <= param);
      }
    }
    return false;
  }, 'File size is too large.');
})(jQuery);
