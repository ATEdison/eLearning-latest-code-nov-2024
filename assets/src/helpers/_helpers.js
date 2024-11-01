(function () {
  window.staHelpers = window.staHelpers || {};

  function minLength(value, minLength) {
    if (typeof value !== 'string') {
      return false;
    }

    return value && value.length >= minLength;
  }

  window.staHelpers.minLength = minLength;

  function containLowercase(value) {
    return /[a-z]/.test(value);
  }

  window.staHelpers.containLowercase = containLowercase;

  function containUppercase(value) {
    return /[A-Z]/.test(value);
  }

  window.staHelpers.containUppercase = containUppercase;

  function containDigit(value) {
    return /[0-9]/.test(value);
  }

  window.staHelpers.containDigit = containDigit;

  function containSpecialCharacter(value) {
    return /[^0-9a-zA-Z]/.test(value);
  }

  window.staHelpers.containSpecialCharacter = containSpecialCharacter;
})();
