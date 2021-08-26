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

    <!-- OneSignal -->
    <!-- <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
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
    </script> -->

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

    <!-- Firebase JS SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script> -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script>
        // importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
        // importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

        var firebaseConfig = {
            apiKey: "AIzaSyBx3AoDZFyoJN-LSr00XW5CkhtrdQekNtc",
            authDomain: "plusphone-agent-d5329.firebaseapp.com",
            projectId: "plusphone-agent-d5329",
            storageBucket: "plusphone-agent-d5329.appspot.com",
            messagingSenderId: "1040633089120",
            appId: "1:1040633089120:web:7d3bfbcd6400f55e3b8705",
            measurementId: "G-TH2BP79THH"
        };
        firebase.initializeApp(firebaseConfig);
        // firebase.analytics();
        const messaging = firebase.messaging();

        // handle incoming message if windows is open
        messaging.onMessage((payload) => {
            console.log('Message received.');
            console.log('Isi: ', payload);
            const notificationOption = {
                body: payload.notification.body,
                icon: payload.notification.icon
            };

            if (Notification.permission === "granted") {

                var notification = new Notification(payload.notification.title, notificationOption);
                notification.onclick = function(ev) {
                    ev.preventDefault();
                    console.log('notif masuk di sini');
                    console.log(payload);
                    window.location.assign('<?= base_url() ?>');
                    notification.close();
                }
            }

        });

        messaging.onTokenRefresh(function() {
            messaging.getToken()
                .then(function(newtoken) {
                    updateTokenNotification(newtoken);
                })
                .catch(function(reason) {
                    console.log(reason);
                })
        })

        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                navigator.serviceWorker.register('<?= base_url() ?>/firebase-messaging-sw.js')
                console.log('Notification permission granted.');
                messaging.getToken()
                    .then((currentToken) => {
                        if (currentToken) {
                            if (window.localStorage.getItem('notification_token') === currentToken)
                                console.log('token is up to date')
                            else
                                updateTokenNotification(currentToken);
                        } else {
                            console.log('No registration token available. Request permission to generate one.');
                        }
                    }).catch((err) => {
                        console.log('An error occurred while retrieving token. ', err);
                    });
            } else {
                console.log('Unable to get permission to notify.');
            }
        });

        function updateTokenNotification(token) {
            $.ajax({
                'url': '<?= base_url() ?>/dashboard/update_token',
                'type': 'POST',
                'dataType': 'JSON',
                'data': {
                    token: token
                }
            }).done(function(response) {
                if (response.success) {
                    console.log('success update token')
                    window.localStorage.setItem('notification_token', token);
                } else console.log('failed update token')
            }).fail(function(response) {
                console.log('failed update token')
                console.log(response);
            });
        }
    </script>

    <?= $this->renderSection('content_js') ?>

</body>

</html>