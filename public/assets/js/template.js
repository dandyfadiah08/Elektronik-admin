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

  // override adminlte card: job make icon change where card-header clickable to expand/collapse card
  $("[data-card-widget='collapse']").unbind();
  $("[data-card-widget='collapse']").click(function () {
    // find the card parent
    var card = $(this).parents(".card").first();
    // find the body and the footer
    var bf = card.find(".card-body, .card-footer");
    if (!card.hasClass("collapsed-card")) {
      // convert minus into plus
      $(this).find(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
      bf.slideUp();
    } else {
      // convert plus into minus
      $(this).find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
      bf.slideDown();
    }
  });
});
