<<<<<<< HEAD
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
=======
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
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
