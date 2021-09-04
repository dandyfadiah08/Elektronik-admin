$(document).ready(function () {
  initDarkMode(isDarkMode());
  $("#darkmode").click(function() {
      if(isDarkMode()) initDarkMode(false);
      else initDarkMode();
  });
});
