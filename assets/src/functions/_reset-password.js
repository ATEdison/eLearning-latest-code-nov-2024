(function ($) {
  $(document).ready(function () {
    formResetPasswordInit();

    $(document).on('input', '.sta-password input[name="password"]', function () {
      var $input = $(this);
      var $meter = $input.parent().find('.progress-bar');
      var $passwordStrength = $input.parent().find('.sta-password-strength');
      var $passwordStrengthDesc = $input.parent().find('.sta-password-strength-desc');
      var password = $input.val();
      var score = wp.passwordStrength.meter(password, wp.passwordStrength.userInputDisallowedList());

      // strength desc
      var strengthDesc = pwsL10n.unknown;
      switch (score) {
        case 1:
          strengthDesc = pwsL10n.short;
          break;
        case 2:
          strengthDesc = pwsL10n.bad;
          break;
        case 3:
          strengthDesc = pwsL10n.good;
          break;
        case 4:
          strengthDesc = pwsL10n.strong;
          break;
        default:
          strengthDesc = pwsL10n.bad;
          break;
      }
      $passwordStrengthDesc.text(strengthDesc);

      // percentage
      var percentage = Math.floor(score / 4 * 100);
      $meter
        .attr('data-score', score)
        .attr('aria-valuenow', percentage)
        .css('width', percentage + '%');

      // min length
      if (window.staHelpers.minLength(password, 6)) {
        $passwordStrength.find('li[data-type="min_length"]').addClass('active');
      } else {
        $passwordStrength.find('li[data-type="min_length"]').removeClass('active');
      }

      // contain lowercase
      if (window.staHelpers.containLowercase(password)) {
        $passwordStrength.find('li[data-type="lowercase"]').addClass('active');
      } else {
        $passwordStrength.find('li[data-type="lowercase"]').removeClass('active');
      }

      // contain uppercase
      if (window.staHelpers.containUppercase(password)) {
        $passwordStrength.find('li[data-type="uppercase"]').addClass('active');
      } else {
        $passwordStrength.find('li[data-type="uppercase"]').removeClass('active');
      }

      // contain digit
      if (window.staHelpers.containDigit(password)) {
        $passwordStrength.find('li[data-type="digit"]').addClass('active');
      } else {
        $passwordStrength.find('li[data-type="digit"]').removeClass('active');
      }

      // contain special character
      if (window.staHelpers.containSpecialCharacter(password)) {
        $passwordStrength.find('li[data-type="special"]').addClass('active');
      } else {
        $passwordStrength.find('li[data-type="special"]').removeClass('active');
      }
    });

    function formResetPasswordInit() {
      var $form = $('.sta-form-reset-password');
      if ($form.length < 1) {
        return;
      }

      var validator = $form.validate({
        ignore: '.ignore',
        errorClass: 'is-invalid',
        rules: {
          password: {
            passwordCheck: true,
          },
          password_confirm: {
            equalTo: '#rp_password',
          },
        },
        errorPlacement: function ($error, $element) {
          // console.log({ error, element });

          // do not show password error message
          if ($element.attr('name') === 'password') {
            $error.remove();
            return;
          }

          $error.insertAfter($element);
        },
      });
    }
  });
})(jQuery);
