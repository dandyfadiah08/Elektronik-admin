<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= env('app.name') ?> | Log In</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>/assets/css/template.css">
  <style>
    .login-card-body .input-group .form-control,
    .register-card-body .input-group .form-control {
      border-right: unset;
    }

    .login-card-body .input-group .input-group-text,
    .register-card-body .input-group .input-group-text {
      background-color: #e9ecef;
      border-left-color: rgb(206, 212, 218);
      border-left-style: solid;
      border-left-width: 1px;
      border-top-right-radius: unset;
      border-bottom-right-radius: unset;
    }
    .bg-white.border-left-0.input-group-text {
      border-top-right-radius: .25rem;
      border-bottom-right-radius: .25rem;
    }
    .login-card-body .input-group .form-control, .register-card-body .input-group .form-control {
      border: 1px solid #ced4da;
      border-top-right-radius: .25rem;
      border-bottom-right-radius: .25rem;
    }

    /* .input-group.has-validation>.input-group-append:nth-last-child(n+3)>.btn, .input-group.has-validation>.input-group-append:nth-last-child(n+3)>.input-group-text, .input-group:not(.has-validation)>.input-group-append:not(:last-child)>.btn, .input-group:not(.has-validation)>.input-group-append:not(:last-child)>.input-group-text, .input-group>.input-group-append:last-child>.btn:not(:last-child):not(.dropdown-toggle), .input-group>.input-group-append:last-child>.input-group-text:not(:last-child), .input-group>.input-group-prepend>.btn, .input-group>.input-group-prepend>.input-group-text {
      border-top-right-radius: unset;
    border-bottom-right-radius: unset;
    } */
  </style>
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
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
            <input id="username" type="text" class="form-control" placeholder="Username">
          </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="password" type="password" class="form-control border-right-0" placeholder="Password">
            <div class="input-group-append">
              <div class="bg-white border-left-0 input-group-text">
                <span class="fas fa-eye btnViewPassword" data-state="hidden" data-target="#password" title="Click to toggle view/hidden password"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div id="grecaptcha" class="g-recaptcha" data-sitekey="<?= $site_key ?>"></div>
              <!-- <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div> -->
            </div>
          </div>
          <div class="row mt-2" style="display: inherit;">
            <div class="col-9 text-danger" id="captcha_error" style="display: none;">Please pass the capthcha</div>
            <div class="col-3 float-right">
              <button type="button" class="btn btn-primary btn-block" id="buttonLogin">Log In</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="<?= base_url() ?>/assets/adminlte3/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?= base_url() ?>/assets/adminlte3/dist/js/adminlte.min.js"></script>
  <script src="<?= base_url() ?>/assets/js/function.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script>
    // var onloadCallback = function() {
    //   grecaptcha.render('grecaptcha', {
    //     'sitekey' : '<?= $site_key ?>'
    //   });
    // };
    $(document).ready(function() {
      $('#buttonLogin').click(function() {
        $('#captcha_error').hide();
        const url = '<?= base_url() ?>/login/doLogin';
        const recaptcha = grecaptcha.getResponse();
        if(recaptcha == '') {
          $('#captcha_error').show();
        } else {
          console.log(recaptcha);
          const $this = $(this);
          const button = $this.html();
          $this.html(`<i class="fas fa-spinner fa-spin"></i>`);
          $this.prop('disabled', true);
          $.ajax({
            url: url,
            method: 'post',
            dataType: 'json',
            data: {
              username: $('#username').val(),
              password: btoa($('#password').val()),
              recaptcha: recaptcha,
            }
          }).done(function(response) {
            console.log(response);
            if (response.success) {
              Swal.fire('Success', response.message, 'success')
              window.location = '<?= base_url() ?>/dashboard';
            } else {
              grecaptcha.reset()
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
        }
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