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
