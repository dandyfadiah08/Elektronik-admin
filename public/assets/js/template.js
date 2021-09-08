$(document).ready(function () {
  initDarkMode(isDarkMode());
  $("#darkmode").click(function () {
    if (isDarkMode()) initDarkMode(false);
    else initDarkMode();
  });

  $(".inputPrice").keyup(function (event) {
    $(this).val(function (index, value) {
      return toPrice(value);
    });
  });

  $(".btnLogout").click(function () {
    window.localStorage.removeItem("notification_token");
  });

  $("#checkNotificationToken").click(function () {
    let token = window.localStorage.getItem("notification_token");
    $.ajax({
      url: $("#base_url").val() + "/dashboard/check_notification_token",
      type: "post",
      dataType: "json",
      data: {
        token: token,
      },
    })
      .done(function (response) {
        var class_swal = response.success ? "success" : "error";
        Swal.fire(response.message, "", class_swal);
      })
      .fail(function (response) {
        Swal.fire("An error occured!", "", "error");
        console.log(response);
      });
  });

  $("#resetNotificationToken").click(function () {
    window.localStorage.removeItem("notification_token");
    $.ajax({
      url: $("#base_url").val() + "/dashboard/reset_notification_token",
      type: "post",
      dataType: "json",
    })
      .done(function (response) {
        var class_swal = response.success ? "success" : "error";
        Swal.fire(response.message, "", class_swal).then(() => {
          if (response.success) {
            window.localStorage.removeItem("notification_token");
            window.location.reload();
          }
        });
      })
      .fail(function (response) {
        Swal.fire("An error occured!", "", "error");
        console.log(response);
      });
  });
});
