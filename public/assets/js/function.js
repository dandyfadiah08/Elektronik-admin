function initDarkMode(on = true) {
  if (on) {
    // turn on darkmode
    console.log("dark mode is on");
    window.localStorage.setItem("darkmode", 1);
    $("#darkmode").html(`<i class="fas fa-sun"></i> Turn off Dark Mode`);
    $("#darkmode").attr("class", "text-warning");
    $("body").addClass("dark-mode");
    $("nav.main-header").addClass("navbar-dark");
    $("nav.main-header").removeClass("navbar-white");
  } else {
    // turn off darkmode
    console.log("dark mode is off");
    window.localStorage.setItem("darkmode", 0);
    $("#darkmode").html(`<i class="fas fa-moon"></i> Turn on Dark Mode`);
    $("#darkmode").attr("class", "text-primary");
    $("body").removeClass("dark-mode");
    $("nav.main-header").removeClass("navbar-dark");
    $("nav.main-header").addClass("navbar-white");
  }
}

function isDarkMode() {
  const darkmode = window.localStorage.getItem("darkmode") ?? "1";
  return darkmode > 0;
}

function toPrice(value) {
  return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function removeComma(value) {
  return value.replace(/[.]/g, "").replace(/[,]/g, "");
}

function substr_replace(str, replace, start, length) {
  // original by: Brett Zamir (https://brett-zamir.me)
  // edited by: Fajar BC
  if (start < 0) start = start < 0 ? start + str.length : start;
  length = length !== undefined ? length : str.length
  length = length < 0 ? length + str.length - start : length;
  return [
    str.slice(0, start),
    replace.substr(0, length),
    replace.slice(length),
    str.slice(start + length)
  ].join('')
}

function masking(input, first_digit = 4, last_digit = 4, replacement = '*') {
  let len = input.length-first_digit-last_digit;
  if(len < 0) return input;
  const masked = replacement.repeat(len);
  return substr_replace(input, masked, first_digit, masked.length);
}

function isInputEmpty(id, first = false, message = 'required.') {
  if($('#'+id).val() == '') {
    if(!first) inputError(id, message)
    return true;
  }
  return false;
}

function isInputZero(id, first = false, message = "required, can't be 0.") {
  const number = $('#'+id).val();
  if(number == '' || Number(number) < 1) {
    if(!first) inputError(id, message)
    return true;
  }
  return false;
}

function inputError(id, message) {
  $(`[for="${id}"]>.invalid-errors`).html(message);
  $("#"+id).addClass('is-invalid');
}

function clearErrors(data) {
  data.forEach(element => {
    $(`[for="${element}"]>.invalid-errors`).html('');
    $("#"+element).removeClass('is-invalid');
  });
}

function checkIsInputEmpty(data, clear_errors = true) {
  let isInvalid = false;
  if(clear_errors) clearErrors(data);
  data.forEach(element => {
    if(isInputEmpty(element)) isInvalid = true;
  });
  return isInvalid;
}

function checkIsInputZero(data, clear_errors = false) {
  let isInvalid = false;
  if(clear_errors) clearErrors(data);
  data.forEach(element => {
    if(isInputZero(element)) isInvalid = true;
  });
  return isInvalid;
}

function togglePassword({event, icon_show = 'fa-unlock', icon_hide = 'fa-lock', with_color = false, color_show = 'success', color_hide = 'danger'}={}) {
  let $event = $($(event.target)[0]);
  const target = $event.data('target');
  const state = $event.data('state');
  if (state == 'show') {
    $event.addClass(icon_hide);
    $event.removeClass(icon_show);
    $event.data('state', 'hidden');
    $(target).attr('type', 'password');
    if(with_color) {
      $event.addClass('text-'+color_hide);
      $event.removeClass('text-'+color_show);
    }
  } else {
    $event.removeClass(icon_hide);
    $event.addClass(icon_show);
    $event.data('state', 'show');
    $(target).attr('type', 'text');
    if(with_color) {
      $event.addClass('text-'+color_show);
      $event.removeClass('text-'+color_hide);
    }
  }
}