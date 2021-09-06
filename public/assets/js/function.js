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
