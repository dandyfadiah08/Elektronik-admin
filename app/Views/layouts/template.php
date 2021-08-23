<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->title ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/dist/css/adminlte.min.css">

    <?= $this->renderSection('content_css') ?>

    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "<?= env('onesignal.app_id') ?>",
                safari_web_id: "<?= env('onesignal.safari_web_id') ?>",
                notifyButton: {
                    enable: true,
                },
                subdomainName: "<?= env('onesignal.localhost') ?>",
            });
        });
    </script>

</head>

<body class="hold-transition sidebar-mini sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('layouts/navbar') ?>

        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- Content -->
        <?= $this->renderSection('content') ?>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2021<?= (int)date('Y') > 2021 ? "-" . date('Y') : '' ?> <a href="<?= base_url() ?>"><?= env('app.name') ?></a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> <?= env('app.version') ?>
            </div>
        </footer>
    </div>

    <script src="<?= base_url() ?>/assets/adminlte3/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/dist/js/adminlte.js"></script>


    <?= $this->renderSection('content_js') ?>

</body>

</html>