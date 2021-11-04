importScripts("https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js");
importScripts(
  "https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"
);

var firebaseConfig = {
  apiKey: "AIzaSyBx3AoDZFyoJN-LSr00XW5CkhtrdQekNtc",
  authDomain: "plusphone-agent-d5329.firebaseapp.com",
  projectId: "plusphone-agent-d5329",
  storageBucket: "plusphone-agent-d5329.appspot.com",
  messagingSenderId: "1040633089120",
  appId: "1:1040633089120:web:7d3bfbcd6400f55e3b8705",
  measurementId: "G-TH2BP79THH",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload
  );
  // Customize notification here
  const notification = JSON.parse(payload);
  const notificationOption = {
    body: notification.body,
    icon: notification.icon,
  };
  // const notificationTitle = 'Background Message Title';
  // const notificationOptions = {
  //   body: 'Background Message body.',
  //   icon: '/firebase-logo.png'
  // };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener("notificationclick", function (event) {
  console.debug("SW notification click event", event);
  const url = "http://localhost:8888/enb/ppa/public/";
  event.waitUntil(
    clients.matchAll({ type: "window" }).then((windowClients) => {
      // Check if there is already a window/tab open with the target URL
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        // If so, just focus it.
        if (client.url === url && "focus" in client) {
          return client.focus();
        }
      }
      // If not, then open the target URL in a new window/tab.
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});
