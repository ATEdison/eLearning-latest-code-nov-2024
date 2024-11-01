/**
 * @see https://clipboardjs.com/
 * @see https://primer.style/css/components/tooltips
 */
(function ($, ClipboardJS) {
  $(document).ready(function () {
    var clipboard = new ClipboardJS('.js-copy-on-click');

    clipboard.on('success', function (e) {
      // console.log(e);
      e.trigger.classList.add('tooltipped', 'tooltipped-n');
    });

    $(document).on('mouseleave', '.js-copy-on-click', function () {
      $(this).removeClass('tooltipped tooltipped-n');
    });
  });
})(jQuery, ClipboardJS);