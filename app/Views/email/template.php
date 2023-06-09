<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> -->
  <style>
    body {
      margin: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #212529;
      text-align: left;
      background-color: #fff;
    }

    :root {
      --primary: #007bff;
      --secondary: #6c757d;
      --success: #28a745;
      --info: #17a2b8;
      --warning: #ffc107;
      --danger: #dc3545;
      --light: #f8f9fa;
      --dark: #343a40;
      --breakpoint-xs: 0;
      --breakpoint-sm: 576px;
      --breakpoint-md: 768px;
      --breakpoint-lg: 992px;
      --breakpoint-xl: 1200px;
      --font-family-sans-serif: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    html {
      font-family: sans-serif;
      line-height: 1.15;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      -ms-overflow-style: scrollbar;
      -webkit-tap-highlight-color: transparent;
    }

    @media (min-width: 576px) {
      .container {
        max-width: 540px;
      }
    }

    @media (min-width: 768px) {
      .container {
        max-width: 720px;
      }
    }

    @media (min-width: 992px) {
      .container {
        max-width: 960px;
      }
    }

    @media (min-width: 1200px) {
      .container {
        max-width: 1140px;
      }
    }

    .container {
      width: 100%;
      padding-right: 15px;
      padding-left: 15px;
      margin-right: auto;
      margin-left: auto;
    }

    .row {
      display: -ms-flexbox;
      display: flex;
      -ms-flex-wrap: wrap;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }

    .col {
      -ms-flex-preferred-size: 0;
      flex-basis: 0;
      -ms-flex-positive: 1;
      flex-grow: 1;
      max-width: 100%;
      position: relative;
      width: 100%;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
    }

    *,
    ::after,
    ::before {
      box-sizing: border-box;
    }

    .card {
      position: relative;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-direction: column;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 1px solid rgba(0, 0, 0, .125);
      border-radius: .25rem;
    }

    .card-body {
      -ms-flex: 1 1 auto;
      flex: 1 1 auto;
      padding: 1.25rem;
    }

    .card-footer:last-child {
      border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
    }

    .card-footer {
      padding: .75rem 1.25rem;
      background-color: rgba(0, 0, 0, .03);
      border-top: 1px solid rgba(0, 0, 0, .125);
    }
    .p-4 {
      padding: 1.5rem !important;
    }

    .p-2, .py-2, .pb-2 {
      padding-bottom: 1rem !important;
    }

    .p-2, .py-2, .pt-2 {
      padding-top: 1rem !important;
    }

    .p-2, .px-2, .pr-2 {
      padding-right: 1rem !important;
    }

    .p-2, .px-2, .pl-2 {
      padding-left: 1rem !important;
    }

    .border-bottom {
      border-bottom: 1px solid #dee2e6 !important;
    }

    .border-top {
      border-top: 1px solid #dee2e6 !important;
    }

    .table {
      width: 100%;
      margin-bottom: 1rem;
      background-color: transparent;
    }

    table {
      border-collapse: collapse;
    }

    .text-center {
      text-align: center !important;
    }

    .col-12 {
      -ms-flex: 0 0 100%;
      flex: 0 0 100%;
      max-width: 100%;
    }

    .col-6 {
      -ms-flex: 0 0 50%;
      flex: 0 0 50%;
      max-width: 50%;
    }

    a {
      color: #007bff;
      text-decoration: none;
      background-color: transparent;
      -webkit-text-decoration-skip: objects;
    }

    img {
      vertical-align: middle;
      border-style: none;
    }

    .small,
    small {
      font-size: 80%;
      font-weight: 400;
    }

    .text-success {
      color: #28a745 !important;
    }

    .img_download {
      height: auto;
      max-height: 32px;
    }

    .justify-center {
      justify-content: center;
    }
  </style>
  <title><?= env('app.name') ?></title>
</head>

<body>
  <div class="container" style="background-color: rgba(100, 100, 150, 0.2);">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <div class="row p-4 border-bottom">
              <div class="col">
                <?= $this->include('email/' . $template) ?>
              </div>
            </div>
            <div class="row p-4 justify-center">
              <div class="col-12 text-center">
                This email is generated automatically. Please do not reply to this email.
                <br>For inquiries and other information, please contact <?= env('app.name') ?> Customer Service
                <br>
                <div class="col-12 px-2 justify-center">
                  <div class="row p-2 justify-center">
                    <div class="col-4 px-2 pb-2">
                      <a class="" href="https://instagram.com/<?= env('app.instagram') ?>" title="Instagram <?= env('app.name') ?>"><img src="<?= base_url('assets/images/instagram.png') ?>" alt="Instagram <?= env('app.name') . " " . env('app.instagram') ?>" height="32"> @<?= env('app.instagram') ?></a>
                    </div>
                    <div class="col-4 px-2 pb-2">
                      <a class="" href="https://twitter.com/<?= env('app.twitter') ?>" title="Twitter <?= env('app.name') ?>"><img src="<?= base_url('assets/images/twitter.png') ?>" alt="Twitter <?= env('app.name') . " " . env('app.instagram') ?>" height="32"> @<?= env('app.twitter') ?></a>
                    </div>
                    <div class="col-4 px-2 pb-2">
                      <a class="" href="https://wa.me/<?= env('app.whatsapp') ?>" title="Whatsapp <?= env('app.name') ?>"><img src="<?= base_url('assets/images/whatsapp.png') ?>" alt="Whatsapp <?= env('app.name') . " " . env('app.instagram') ?>" height="32"> <?= preg_replace('~(\d{2})(\d{3})(\d{3})(\d{4}).*~', '+$1 $2-$3-$4', env('app.whatsapp')) ?></a>
                    </div>
                  </div>
                </div>
                <div class="col-12 border-top">
                  <div class="row py-2">
                    <div class="col-6 text-center">
                      Download: <b><?= env('app2.name') . " - " . env('app2.tagline') ?></b>
                    </div>
                    <div class="col-6 text-center">
                      Download: <b><?= env('app1.name') . " - " . env('app1.tagline') ?></b>
                    </div>
                  </div>
                  <div class="row pb-2">
                    <div class="col-6">
                      <div class="text-center">
                        <div class="row">
                          <div class="col-12 pt-2">
                            <a href="<?= env('app2.playstore_link') ?>" title="Download <?= env('app2.name') ?> from Google Playstore"><img src="<?= base_url('assets/images/android-download.png') ?>" alt="Download <?= env('app2.name') ?> on Google Playstore" class="img_download"></a>
                          </div>
                          <div class="col-12 pt-2">
                            <a href="<?= env('app2.appstore_link') ?>" title="Download <?= env('app2.name') ?> from Apple App Store"><img src="<?= base_url('assets/images/ios-download.png') ?>" alt="Download <?= env('app2.name') ?> on Apple App Store" class="img_download"></a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <div class="row">
                          <div class="col-12 pt-2">
                            <a href="<?= env('app1.playstore_link') ?>" title="Download <?= env('app1.name') ?> from Google Playstore"><img src="<?= base_url('assets/images/android-download.png') ?>" alt="Download <?= env('app1.name') ?> on Google Playstore" class="img_download"></a>
                          </div>
                          <div class="col-12 pt-2">
                            <a href="<?= env('app1.appstore_link') ?>" title="Download <?= env('app1.name') ?> from Apple App Store"><img src="<?= base_url('assets/images/ios-download.png') ?>" alt="Download <?= env('app1.name') ?> on Apple App Store" class="img_download"></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col">
                  <small>
                    DISCLAIMER:
                    <br><span class="text-success">Think environmental sustainability before printing this email.</span>
                    <br>Caution: This email (including any attachments) is only addressed to the recipients of the emails listed above and may not be misused by anyone. If you are not the intended recipient of the email, you may not forward, distribute, distribute, lend, print, duplicate, or otherwise make use of this email.
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>

</html>