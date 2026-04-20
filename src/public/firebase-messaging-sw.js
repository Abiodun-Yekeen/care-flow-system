importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyB3HpR6J8Qyz_28zFHtka-xGcM3H2J3otE",
    projectId: "careflow-83b22",
    messagingSenderId: "728170256522",
    appId: "1:728170256522:web:c060417f56ed0bc5e2b573",
});


const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: "/icon.png"
    });
});
