(function () {
    'use strict';

    var installPrompt = null;
    var installButton = document.getElementById('install-pwa-admin');
    var offlineBanner = document.getElementById('admin-offline-banner');

    function updateConnectionStatus() {
        if (offlineBanner) {
            offlineBanner.style.display = navigator.onLine ? 'none' : 'block';
        }
    }

    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);
    updateConnectionStatus();

    // Check if app is already running in standalone mode (installed)
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone || document.referrer.includes('android-app://');

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        installPrompt = event;
        if (installButton && !isStandalone) {
            installButton.style.display = 'inline-flex';
        }
    });

    window.addEventListener('appinstalled', function () {
        if (installButton) {
            installButton.style.display = 'none';
        }
        installPrompt = null;
    });

    if (installButton) {
        installButton.addEventListener('click', function (e) {
            e.preventDefault();
            if (installPrompt) {
                installPrompt.prompt();
                installPrompt.userChoice.then(function (choiceResult) {
                    if (choiceResult.outcome === 'accepted') {
                        installButton.style.display = 'none';
                    }
                    installPrompt = null;
                });
            } else {
                alert('Để cài đặt ứng dụng Admin, bạn có thể nhấn vào biểu tượng Cài đặt trên thanh địa chỉ (URL bar) của trình duyệt hoặc vào Menu (⋮) -> chọn "Cài đặt ứng dụng".');
            }
        });
    }

    if ('serviceWorker' in navigator && window.adminPwaConfig) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register(window.adminPwaConfig.swUrl, {
                scope: window.adminPwaConfig.scope
            }).catch(function (err) {
                console.warn('Admin PWA Service Worker Registration:', err);
            });
        });
    }
})();
