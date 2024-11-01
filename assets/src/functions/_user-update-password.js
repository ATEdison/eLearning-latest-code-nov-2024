(function ($) {
  $(document).ready(function () {
    var $form = $('.sta-form-password');
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
  });
})(jQuery);
