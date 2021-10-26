<<<<<<< HEAD
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
            window.location.assign(`${base_url}`);
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
        navigator.serviceWorker.register(`${base_url}/firebase-messaging-sw.js`)
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
        'url': `${base_url}/dashboard/update_token`,
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
=======
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
            window.location.assign(`${base_url}`);
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
        navigator.serviceWorker.register(`${base_url}/firebase-messaging-sw.js`)
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
        'url': `${base_url}/dashboard/update_token`,
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
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
}