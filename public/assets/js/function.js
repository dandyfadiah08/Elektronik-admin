function initDarkMode(on = true) {
  if (on) {
    // turn on darkmode
    // console.log("dark mode is on");
    window.localStorage.setItem("darkmode", 1);
    $("#darkmode").html(`<i class="fas fa-sun"></i> Turn off Dark Mode`);
    $("#darkmode").attr("class", "col text-warning");
    $("body").addClass("dark-mode");
    $("nav.main-header").addClass("navbar-dark");
    $("nav.main-header").removeClass("navbar-white");
  } else {
    // turn off darkmode
    // console.log("dark mode is off");
    window.localStorage.setItem("darkmode", 0);
    $("#darkmode").html(`<i class="fas fa-moon"></i> Turn on Dark Mode`);
    $("#darkmode").attr("class", "col text-primary");
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
  value = Number(value).toString();
  return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function removeComma(value) {
  return value.replace(/[.]/g, "").replace(/[,]/g, "");
}

function substr_replace(str, replace, start, length) {
  // original by: Brett Zamir (https://brett-zamir.me)
  // edited by: Fajar BC
  if (start < 0) start = start < 0 ? start + str.length : start;
  length = length !== undefined ? length : str.length;
  length = length < 0 ? length + str.length - start : length;
  return [
    str.slice(0, start),
    replace.substr(0, length),
    replace.slice(length),
    str.slice(start + length),
  ].join("");
}

function masking(input, first_digit = 4, last_digit = 4, replacement = "*") {
  let len = input.length - first_digit - last_digit;
  if (len < 0) return input;
  const masked = replacement.repeat(len);
  return substr_replace(input, masked, first_digit, masked.length);
}

function isInputEmpty(id, first = false, message = "required.") {
  if ($("#" + id).val() == "") {
    if (!first) inputError(id, message);
    return true;
  }
  return false;
}

function isInputZero(id, first = false, message = "required, can't be 0.") {
  const number = $("#" + id).val();
  if (number == "" || Number(number) < 1) {
    if (!first) inputError(id, message);
    return true;
  }
  return false;
}

function inputError(id, message) {
  $(`[for="${id}"]>.invalid-errors`).html(message);
  $("#" + id).addClass("is-invalid");
}

function clearErrors(data) {
  data.forEach((element) => {
    $(`[for="${element}"]>.invalid-errors`).html("");
    $("#" + element).removeClass("is-invalid");
  });
}

function checkIsInputEmpty(data, clear_errors = true) {
  let isInvalid = false;
  if (clear_errors) clearErrors(data);
  data.forEach((element) => {
    if (isInputEmpty(element)) isInvalid = true;
  });
  return isInvalid;
}

function checkIsInputZero(data, clear_errors = false) {
  let isInvalid = false;
  if (clear_errors) clearErrors(data);
  data.forEach((element) => {
    if (isInputZero(element)) isInvalid = true;
  });
  return isInvalid;
}

function togglePassword({
  event,
  icon_show = "fa-eye-slash",
  icon_hide = "fa-eye",
  with_color = false,
  color_show = "success",
  color_hide = "danger",
} = {}) {
  let $event = $($(event.target)[0]);
  const target = $event.data("target");
  const state = $event.data("state");
  if (state == "show") {
    $event.addClass(icon_hide);
    $event.removeClass(icon_show);
    $event.data("state", "hidden");
    $(target).attr("type", "password");
    if (with_color) {
      $event.addClass("text-" + color_hide);
      $event.removeClass("text-" + color_show);
    }
  } else {
    $event.removeClass(icon_hide);
    $event.addClass(icon_show);
    $event.data("state", "show");
    $(target).attr("type", "text");
    if (with_color) {
      $event.addClass("text-" + color_show);
      $event.removeClass("text-" + color_hide);
    }
  }
}

function checkIfChecked(element) {
  let isChecked = false;
  let checkboxes = $(element);
  for (const [key, value] of Object.entries(checkboxes)) {
    if (value.checked) {
      isChecked = true;
      return true;
    }
  }
  return isChecked;
}

function btnOnLoading(
  target,
  loading = true,
  newHTML = "Doing magic..",
  icon = `<i class="fas fa-spinner fa-spin"></i> `
) {
  if (loading) {
    $(target).prop("disabled", true);
    const thisHTML = $(target).html();
    $(target).html(icon + newHTML);
    return thisHTML;
  } else {
    $(target).prop("disabled", false);
    $(target).html(newHTML);
  }
}

function popupPrint(data, height = 600, width = 800, timeout = 1234) {
  const header = $("head").html();
  var mywindow = window.open("", "new div", `height=${height},width=${width}`);
  mywindow.document.write(`<html>
  ${header}
  <body>${data}</body></html>`);
  mywindow.document.close();
  mywindow.focus();
  setTimeout(function () {
    mywindow.print();
    mywindow.close();
  }, timeout);

  return true;
}

function removeTags(html) {
  var div = document.createElement("div");
  div.innerHTML = html;
  const text = div.textContent || div.innerText || "";
  return text;
}

function htmlentities(str) {
  return str.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
    return '&#'+i.charCodeAt(0)+';';
  })
}

function noticeDefault(data) {
  const sound = data.sound || true;
  if (sound) playSound("message");
  return new jBox("Notice", {
    content: removeTags(data.message),
    color: data.color || "black",
    autoClose: data.autoClose || 3000,
    attributes: {
      x: data.x || "right",
      y: data.y || "bottom",
    },
    ignoreDelay: data.ignoreDelay || true,
  });
}

function myNotification(data) {
  var title = data.title || "Notification",
    subtitle = data.subtitle || "",
    with_subtitle = data.with_subtitle || true;

  if (subtitle == "" && with_subtitle) {
    subtitle = new Date().toLocaleTimeString();
  }
  if (data.sound) {
    // var sound = new Howl({
    //   src: [base_url+'/assets/notification.mp3']
    // });
    // sound.play();
    playSound(data.soundSource || "notification");
  }
  $(document).Toasts("create", {
    class: data.class || "bg-danger",
    title: title,
    subtitle: subtitle,
    autohide: data.autohide || true,
    timerProgressBar: data.timerProgressBar || true,
    delay: data.delay || 5000,
    body: data.body || "",
  });
}

function btnDatatable(data) {
  return {
    text: data.text || `<i class="fas fa-plus"></i> Add`,
    className: data.class || "btn-success",
    action: () => data.cb(),
  };
}

function btnRefresh(cb) {
  var newData = {
    text: `<i class="fas fa-sync-alt btnRefresh" title="Refresh Data"></i>`,
    class: "btn-warning",
    cb: () => {
      let msg = noticeDefault({ message: "Reloading..", autoClose: 750 });
      $(".btnRefresh").addClass("fa-spin");
      setTimeout(() => $(".btnRefresh").removeClass("fa-spin"), 750);
      cb();
    },
  };
  return btnDatatable(newData);
}

function playSound(source) {
  var sound = new Howl({
    src: [`${base_url}/assets/sounds/${source || "ok"}.mp3`],
  });
  sound.play();
}

function changeCountBadge(element, plus = true) {
  var count = Number($("#" + element).text());
  if (plus) {
    count++;
  } else {
    if (count > 0) count--;
    if (count <= 0) count = "";
  }
  $("." + element).text(count);
}

function fallbackCopyTextToClipboard(text) {
  var textArea = document.createElement("textarea");
  textArea.innerHTML = text;

  // Avoid scrolling to bottom
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.position = "fixed";

  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    var successful = document.execCommand("copy");
    if (successful)
      noticeDefault({
        message: "Copying text was success",
        color: "green",
      });
    else
      noticeDefault({
        message: "Copying text was failed",
        color: "red",
      });
  } catch (err) {
    console.error("Fallback: Oops, unable to copy", err);
  }

  document.body.removeChild(textArea);
}

function copyTextToClipboard(text) {
  if (!navigator.clipboard) {
    fallbackCopyTextToClipboard(text);
    return;
  }
  navigator.clipboard.writeText(text).then(
    function () {
      noticeDefault({
        message: "Text copied!",
        color: "green",
      });
    },
    function (err) {
      console.error("Async: Could not copy text: ", err);
    }
  );
}

function initDateRangePicker(
  element = ".datepicker",
  callback = false,
  options = {}
) {
  let default_options = {
    startDate: moment().startOf("month"),
    showDropdowns: true,
    minYear: 2020,
    maxYear: moment().year(),
    maxSpan: {
      days: 90,
    },
    ranges: {
      Today: [moment(), moment()],
      Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "Last 7 Days": [moment().subtract(6, "days"), moment()],
      "Last 30 Days": [moment().subtract(29, "days"), moment()],
      "This Month": [moment().startOf("month"), moment().endOf("month")],
      "Last Month": [
        moment().subtract(1, "month").startOf("month"),
        moment().subtract(1, "month").endOf("month"),
      ],
    },
    locale: {
      format: "YYYY-MM-DD",
      separator: " / ",
      monthNames: moment.months(),
    },
  };
  for (const [key, val] of Object.entries(options)) {
    default_options[key] = val;
  }
  options = default_options;
  $(element)
    .daterangepicker(options)
    .on("apply.daterangepicker", function (ev, picker) {
      if (callback) callback();
    });
}

function exportData(data, export_path = "/export", downloadMessage = null) {
  $.ajax({
    url: base_url + path + export_path,
    type: "post",
    dataType: "json",
    data: data,
  })
    .done(function (response) {
      if (response.success) {
        let msg = noticeDefault(
          downloadMessage || {
            message: "Downloading..",
            autoClose: 2000,
            color: "green",
          }
        );
        window.open(response.data);
      } else if (Object.keys(response.data).length > 0) {
        for (const [key, value] of Object.entries(response.data)) {
          inputError(key, value);
        }
      } else Swal.fire(response.message, "", "error");
    })
    .fail(function (response) {
      Swal.fire("An error occured!", "", "error");
    })
    .always(function () {
      $(".btnExport").removeClass("do-animation");
    });
}

function iconCopy(text_to_copy, title = "Click to copy") {
  return `<small><i class="fas fa-copy pointer" title="${title}" data-copy="${text_to_copy}"></i></small>`;
}

function refreshPaymentGatewayBalance() {
  const $loading = $("#payment_gateway_balance > i");
  $loading.addClass("fa-spin");
  $.ajax({
    url: $("#base_url").val() + "/dashboard/payment_gateway_balance",
    type: "post",
    dataType: "json",
  })
    .done(function (response) {
      if (response.success) {
        const $balance = $("#payment_gateway_balance > span");
        $balance.data("balance", response.data.balance);
        $balance.text("IDR "+toPrice(response.data.balance));
      }
      noticeDefault({
        message: response.message,
        color: response.success ? "green" : "red",
      });
    })
    .fail(function (response) {
      Swal.fire("An error occured!", "", "error");
      console.log(response);
    })
    .always(function () {
      $loading.removeClass("fa-spin");
    });
}
