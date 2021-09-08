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
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/template.css">

    <?= $this->renderSection('content_css') ?>
</head>

<body class="hold-transition sidebar-mini dark-mode">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('layouts/navbar') ?>

        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar') ?>
        
        <!-- Control Sidebar -->
        <?= $this->include('layouts/control-sidebar') ?>

        <!-- Content -->
        <?= $this->renderSection('content') ?>


        <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
        </a>
            <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2021<?= (int)date('Y') > 2021 ? "-" . date('Y') : '' ?> <a href="<?= base_url() ?>"><?= env('app.name') ?></a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> <?= env('app.version') ?>
            </div>
        </footer>
        <input type="hidden" id="base_url" value="<?= base_url() ?>">
    </div>

    <!-- JS Constants -->
    <script>
        const base_url = '<?= base_url() ?>';
    </script>

    <!-- JS function  -->
    <script src="<?= base_url() ?>/assets/js/function.js"></script>

    <!-- JS default  -->
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/adminlte3/dist/js/adminlte.js"></script>
    
    <!-- Sweet Alert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Firebase JS SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script> -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="<?= base_url() ?>/assets/js/firebase.js"></script>
    
    <!-- OneSignal -->
    <!-- <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script src="<?= base_url() ?>/assets/js/onesignal.js"></script> -->

    <!-- JS custom  -->
    <script src="<?= base_url() ?>/assets/js/template.js"></script>

    <?= $this->renderSection('content_js') ?>

</body>

</html>