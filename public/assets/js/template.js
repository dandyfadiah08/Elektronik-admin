$(document).ready(function () {
  initDarkMode(isDarkMode());
  $("#darkmode").click(function() {
      if(isDarkMode()) initDarkMode(false);
      else initDarkMode();
  });

  $('.inputPrice').keyup(function(event) {
    $(this).val(function(index, value) {
        return toPrice(value);
    });
});

});
