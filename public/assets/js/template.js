$(document).ready(function () {
  initDarkMode(isDarkMode());
  $("#darkmode").click(function () {
    if (isDarkMode()) initDarkMode(false);
    else initDarkMode();
  });

  if (typeof io !== "undefined") {
    var node_server = nodejs_url;
    socket = io.connect(node_server, {});

    socket.on("notification", function (data) {
      // console.log("notification")
      // console.log(data)
      if (data.type != 1) myNotification(data);
      else noticeDefault(data);
    });

    socket.on("new-data", function (data) {
      // console.log("new-data")
      // console.log(data)
      var unreviewed_count = Number($("#unreviewed_count").text()) + 1;
      $(".unreviewed_count").text(unreviewed_count);
      myNotification({
        type: 2,
        title: `Alert!`,
        body: `<b>${data.check_code}</b> need review! <a href="${base_url}/device_check/detail/${data.check_id}" class="btn btn-sm btn-success" target="_blank">OPEN</a>`,
        class: "bg-warning",
        delay: 15000,
        sound: true,
        soundSource: "new-data",
      });
    });

    socket.on("new-appointment", function (data) {
      // console.log("new-appointment")
      // console.log(data)
      var transaction_count = Number($("#transaction_count").text()) + 1;
      $(".transaction_count").text(transaction_count);
      myNotification({
        type: 2,
        title: `Alert!`,
        body: `<b>${data.check_code}</b> need appointment confirmation! <a href="${base_url}/transaction/?s=${data.check_code}" class="btn btn-sm btn-success" target="_blank">OPEN</a>`,
        class: "bg-primary",
        delay: 15000,
        sound: true,
        soundSource: "new-appointment",
      });
    });

    socket.on("new-withdraw", function (data) {
      // console.log("new-withdraw")
      // console.log(data)
      var withdraw_count = Number($("#withdraw_count").text()) + 1;
      $(".withdraw_count").text(withdraw_count);
      myNotification({
        type: 2,
        title: `Alert!`,
        body: `New withdraw request for <b>${data.account_number}</b> ! <a href="${base_url}/withdraw/?s=${data.account_number}" class="btn btn-sm btn-primary" target="_blank">OPEN</a>`,
        class: "bg-success",
        delay: 15000,
        sound: true,
        soundSource: "new-withdraw",
      });
    });
  }

  let firstOpen = true;
  socket.on("connect", () => {
    console.log("socket connected");
    if (!firstOpen)
      noticeDefault({
        message: "Realtime notification is connected.",
        color: "green",
      });
    firstOpen = false;
  });

  socket.on("disconnect", () => {
    console.log("socket disconnected");
    noticeDefault({
      message: "Realtime notification was disconnected.",
      color: "red",
    });
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
