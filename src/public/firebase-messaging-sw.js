importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");


firebase.initializeApp({
    apiKey: "AIzaSyB3HpR6J8Qyz_28zFHtka-xGcM3H2J3otE",
    projectId: "careflow-83b22",
    messagingSenderId: "728170256522",
    appId: "1:728170256522:web:c060417f56ed0bc5e2b573",
});


const messaging = firebase.messaging();

// This handles the notification when the app is in the background or closed
messaging.onBackgroundMessage((payload) => {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/logo.jpeg'
    });
});


//Handle notification click to open the Inbox
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/my-desk/files') // Redirect user to their inbox
    );
});


