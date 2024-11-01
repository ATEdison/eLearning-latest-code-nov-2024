(function () {
  // wait for window loaded
  let isWindowLoaded = false;
  window.addEventListener('load', function () {
    isWindowLoaded = true;
  }, false);

  document.addEventListener('lazybeforeunveil', function (e) {
    // console.log(e.target.tagName);
    if (e.target.tagName !== 'IFRAME') {
      return;
    }

    e.preventDefault();

    var src = e.target.getAttribute('data-src');
    if (src) {
      if (isWindowLoaded) {
        e.target.setAttribute('src', src);
      } else {
        const waitForWindowLoadedInterval = setInterval(function () {
          // console.log(`waitForWindowLoadedInterval`, {src});
          if (isWindowLoaded) {
            clearInterval(waitForWindowLoadedInterval);
            e.target.setAttribute('src', src);
          }
        }, 200);
      }
    }
  });
})();
