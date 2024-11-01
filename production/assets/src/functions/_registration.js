(function ($) {
  $(document).ready(function () {
    var $form = $('.sta-form-registration');
    if ($form.length < 1) {
      return;
    }

    var validator = $form.validate({
      ignore: '.ignore',
      errorClass: 'is-invalid',
      rules: {
        password_confirm: {
          equalTo: '#rp_password',
        },
        profile_image_file: {
          fileSizeMax: 1024 * 1024,
        },
      },
      messages: {
        profile_image_file: {
          fileSizeMax: 'Maximum file size is 1MB.',
        },
      },
      errorPlacement: function ($error, $element) {
        // console.log({ error, element });

        if ($element.is(':radio')) {
          $error.insertAfter($element.closest('fieldset').find('> :last-child'));
          return;
        }

        if ($element.is(':checkbox')) {
          $error.insertAfter($element.next('label'));
          return;
        }

        if ($element.attr('name') === 'profile_image_file') {
          $error.insertAfter($element.closest('.sta-image-upload'));
          return;
        }

        $error.insertAfter($element);
      },
      invalidHandler: function (event, validator) {
        // 'this' refers to the form
        var errorCount = validator.numberOfInvalids();
        if (errorCount > 0) {
          window.staHelpers.animateTo($(validator.errorList[0].element));
        }
        // console.log({ event, validator });
        // window.staValidator = validator;
        updateFormStep();
      },
      success: function ($label) {
        $label.remove();
        updateFormStep();
      }
    });

    $(document).on('input', '.sta-form-registration input', function () {
      removeServerError($(this));
      updateFormStep();
    });

    $(document).on('change', '.sta-form-registration select', function () {
      removeServerError($(this));
      updateFormStep();
    });

    function removeServerError($field) {
      $field.parent().find('div.is-invalid').remove();
    }

    function updateFormStep() {
      var formStep = 1;
      $('.sta-reg-step').each(function () {
        var $step = $(this);
        var stepIndex = $step.index() + 1;
        var passValidation = true;

        // validate fields which is not radio or checkbox
        $step.find('[required]:not([type="radio"]):not([type="checkbox"])').each(function () {
          var $item = $(this);
          var fieldName = $item.attr('name');

          // console.log({ fieldName, validator });

          if (!$item.val() || (validator.invalid.hasOwnProperty(fieldName) && validator.invalid[fieldName])) {
            passValidation = false;
            return false;
          }
        });

        // console.log('1', { stepIndex, passValidation });

        // do not pass validation, break loop
        if (!passValidation) {
          return false;
        }

        // validate checkbox and radio fields
        var checkFields = {};
        $step.find('[required][type="radio"], [required][type="checkbox"]').each(function () {
          var $item = $(this);
          var fieldName = $item.attr('name');
          if (!checkFields.hasOwnProperty(fieldName)) {
            checkFields[fieldName] = false;
          }
          checkFields[fieldName] |= $item.is(':checked');
        });

        $.each(checkFields, function (field, isChecked) {
          if (!isChecked) {
            passValidation = false;
            return false;
          }
        });

        // console.log('2', { stepIndex, passValidation });

        // do not pass validation, break loop
        if (!passValidation) {
          return false;
        }

        // passed validation, increase form step
        formStep = stepIndex + 1;
        // console.log('3', { stepIndex, formStep });
      });

      // update form step
      $form.attr('data-step', formStep);
    }
  });
})(jQuery);
