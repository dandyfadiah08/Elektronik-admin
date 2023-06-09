<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->title ?? env('app.name') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>/assets/images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jbox/jBox.all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/dark-mode.css?v=20220214">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/animation.css?v=20211110">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/template.css?v=20220321">

    <?= $this->renderSection('content_css') ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('layouts/navbar') ?>

        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- Control Sidebar -->
        <?= $this->include('layouts/control-sidebar') ?>

        <!-- Content -->
        <?= $this->renderSection('content') ?>

        <input type="hidden" id="base_url" value="<?= base_url() ?>">
    </div>

    <!-- JS Constants -->
    <script>
        const base_url = '<?= base_url() ?>';
        const nodejs_url = '<?= env('nodejs.url') . (empty(env('nodejs.path')) ? ':' . env('nodejs.port') : '') ?>'
        const nodejs_path = '<?= env('nodejs.path') ?>'
        const _username = '<?= session()->username ?>';
    </script>

    <!-- JS function  -->
    <script src="<?= base_url() ?>/assets/js/function.js?v=20220513"></script>

    <!-- JS default  -->
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/dist/js/adminlte.js"></script>
    <script src="<?= base_url() ?>/assets/libraries/socket.io/socket.io.min.js"></script>
    <script src="<?= base_url() ?>/assets/libraries/howler/howler.core.min.js"></script>
    <script src="<?= base_url() ?>/assets/libraries/jbox/jBox.all.min.js"></script>

    <!-- Sweet Alert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Firebase JS SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script> -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="<?= base_url() ?>/assets/js/firebase.js?v=20211110"></script>

    <!-- OneSignal -->
    <!-- <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script src="<?= base_url() ?>/assets/js/onesignal.js"></script> -->

    <!-- JS custom  -->
    <script src="<?= base_url() ?>/assets/js/template.js?v=20220405"></script>

    <?= $this->renderSection('content_js') ?>
</body>

</html>