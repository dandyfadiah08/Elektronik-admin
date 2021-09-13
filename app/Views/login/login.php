<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= env('app.name') ?> | Log in</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/css/template.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="<?= base_url() ?>"><?= env('app.name') ?></a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="" method="post">
          <div class="input-group mb-3">
            <input id="username" type="text" class="form-control" placeholder="Username">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input id="password" type="password" class="form-control" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock btnViewPassword" data-state="hidden" data-target="#password" title="Click to toggle view/hidden password"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="button" class="btn btn-primary btn-block" id="buttonLogin">Log In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <script src="<?= base_url() ?>/assets/adminlte3/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/dist/js/adminlte.min.js"></script>
  <script src="<?= base_url() ?>/assets/js/function.js"></script>
  <script>
    $(document).ready(function() {
      $('#buttonLogin').click(function() {
        const url = '<?= base_url() ?>/login/doLogin';
        const $this = $(this);
        const button = $this.html();
        $this.html(`<i class="fas fa-spinner fa-spin"></i>`);
        $this.prop('disabled', true);
        console.log(url);
        $.ajax({
          url: url,
          method: 'post',
          dataType: 'json',
          data: {
            username: $('#username').val(),
            password: $('#password').val(),
          }
        }).done(function(response) {
          console.log(response);
          if (response.success) {
            Swal.fire('Success', response.message, 'success')
            window.location = '<?= base_url() ?>/dashboard';
          } else {
            Swal.fire('Failed', response.message, 'error')
          }
        }).fail(function(response) {
          console.log('fail');
          console.log(response);
          Swal.fire('Error occured', '', 'error')
        }).always(function() {
          $this.html(button);
          $this.prop('disabled', false);
        })
        return false;
      });

      $('div > span > .btnViewPassword, .btnViewPassword').click(function(e) {
        togglePassword({
          event: e,
          with_color: true,
          color_hide: 'secondary'
        });
      });

    })
  </script>
</body>

</html>