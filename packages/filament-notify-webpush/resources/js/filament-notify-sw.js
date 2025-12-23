self.addEventListener('push', function (event) {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'اعلان جدید';
    const options = {
        body: data.body || '',
        icon: data.icon || undefined,
        data: {
            url: data.url || null,
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data && event.notification.data.url;
    if (url) {
        event.waitUntil(clients.openWindow(url));
    }
});
