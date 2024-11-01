(function ($, WebFont) {
  'use strict';

  // global
  WebFont.load({
    google: {
      families: ['Open+Sans:wght@400;700&display=swap'],
      version: 2,
    },
    // custom: {
    //   families: ['Gotham', 'Displace 2.0'],
    //   urls: [staSettings.fontFamiliesUrl]
    // },
    active: function () {
      //when fonts loaded
      // console.log('fonts loaded');
      $(document).trigger('font_loaded');
    }
  });
})(jQuery, WebFont);
