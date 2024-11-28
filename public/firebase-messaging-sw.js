
importScripts('https://www.gstatic.com/firebasejs/9.20.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.20.0/firebase-messaging-compat.js');

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SETUP') {
        if (!firebase.apps.length) {
            firebase.initializeApp(event.data.config);
        }
        const messaging = firebase.messaging();

        messaging.setBackgroundMessageHandler(function(payload) {
            console.log("Background message received.", payload);

            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.image
            };

            return self.registration.showNotification(notificationTitle, notificationOptions);
        });
    }
});
self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function(event) {
    const messageData = event.data.json();
    console.log('Push message received:', messageData);
    const notificationOptions = {
        body: messageData.notification.body,
        icon: messageData.notification.image,
        data: {
            click_action: 'ACTION_URL',
        }
    };
    self.clients.matchAll().then(clients => {
        clients.forEach(client => {
            client.postMessage({
                type: "Notification received",
                payload: messageData
            });
        });
    });

    event.waitUntil(
        self.registration.showNotification(messageData.notification.title, notificationOptions)
    );
});

self.addEventListener('notificationclick', function(event) {
    console.log('On notification click: ', event.notification.tag);
    event.notification.close();

    event.waitUntil(
        clients.matchAll({
            type: "window"
        }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === 'http://localhost:8080/appdichvuthuongmai/admin/orders/' && 'focus' in client)
                    return client.focus();
            }
            if (clients.openWindow)
                return clients.openWindow('http://localhost:8080/apptmdt/admin/orders/');
        })
    );
});
