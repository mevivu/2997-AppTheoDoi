<script>
    $(document).ready(function() {
        reloadData();
        const firebaseConfig = {
            apiKey: "{{ config('firebase.api_key') }}",
            authDomain: "{{ config('firebase.auth_domain') }}",
            projectId: "{{ config('firebase.project_id') }}",
            storageBucket: "{{ config('firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
            appId: "{{ config('firebase.app_id') }}",
            measurementId: "{{ config('firebase.measurement_id') }}"
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const messaging = firebase.messaging();

        navigator.serviceWorker.addEventListener('message', function(event) {
            // console.log("Message from Service Worker:", event.data);
            reloadData()
        });

        function registerServiceWorker() {
            if ("serviceWorker" in navigator) {
                // console.log('Service worker is supported');

                navigator.serviceWorker.register(`${urlHome}/firebase-messaging-sw.js`)
                    .then(registration => {
                        if (registration.active) {
                            // console.log('Service worker already active');
                            initMessaging(registration);
                        } else {
                            registration.addEventListener('updatefound', () => {
                                const newWorker = registration.installing;
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'activated') {
                                        sendConfigToServiceWorker(newWorker);
                                        initMessaging(registration);
                                    }
                                });
                            });
                        }
                    })
                    .catch(err => console.error('Service worker registration failed:', err));
            }
        }

        function sendConfigToServiceWorker(worker) {
            worker.postMessage({
                type: 'SETUP',
                config: firebaseConfig,
                userId: getUserId()
            });
        }

        function initMessaging(registration) {
            // console.log('Service worker registration successful:', registration);
            messaging.useServiceWorker(registration);
            requestNotificationPermission(registration);
            handleTokenRefresh(registration);
        }

        function requestNotificationPermission(registration) {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    // console.log("Notification permission granted.");
                    retrieveAndUpdateToken(registration);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng bật thông báo để nhận thông tin!',
                    });
                }
            });
        }

        function retrieveAndUpdateToken(registration) {
            messaging.getToken({
                    vapidKey: "{{ config('firebase.vapid_key') }}",
                    serviceWorkerRegistration: registration
                })
                .then(token => {
                    if (token) {
                        console.log('Current token for client:', token);
                        $('input[name="device_token"]').val(token);
                        if (userIsLoggedIn()) {
                            updateDeviceToken(token);
                        } else {
                            // console.log('User not logged in, skipping server update for device token.');
                        }
                    } else {
                        // console.warn('No registration token available. Request permission to generate one.');
                    }
                })
                .catch(err => console.error('Error retrieving token:', err));
        }

        function updateDeviceToken(deviceToken) {
            const userId = getUserId();
            if (!userId) {
                console.log('No user logged in, cannot update token on server');
                return;
            }

            $.ajax({
                url: `${urlHome}/admin/thong-bao/update-device-token`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                data: {
                    device_token: deviceToken
                },
                success: (data) => console.log(data.message),
                error: err => {
                    console.error('Error updating token on server:', err);
                }
            });
        }

        function userIsLoggedIn() {
            return !!getUserId();
        }

        function getUserId() {
            let userId = null;
            @if (auth('admin')->check())
                userId = @json(auth('admin')->user()->id);
            @endif
            return userId;
        }

        function handleTokenRefresh(registration) {
            messaging.onTokenRefresh(() => retrieveAndUpdateToken(registration));
        }

        function timeSince(date) {
            const now = new Date();
            const timestamp = new Date(date);

            const seconds = Math.floor((now - timestamp) / 1000);
            if (seconds < 60) {
                return `${seconds} giây trước`;
            }

            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) {
                return `${minutes} phút trước`;
            }

            const hours = Math.floor(minutes / 60);
            if (hours < 24) {
                return `${hours} giờ trước`;
            }

            const days = Math.floor(hours / 24);
            return `${days} ngày trước`;
        }

        function renderNotifications(notifications) {
            // Cập nhật badge số lượng thông báo
            const $badge = $('#message-box .badge');
            $badge.text(notifications?.length || 0);

            // Thêm class để hiệu ứng khi có thông báo mới
            if (notifications?.length > 0) {
                $badge.addClass('badge-pulse');
            }

            const $messageBox = $('#message-box .dropdown-menu');
            $messageBox.empty();

            // Thêm header cho dropdown
            $messageBox.append(`
                <div class="notification-header">
                    <h6 class="mb-0 text-primary">Thông báo của bạn</h6>
                    <small class="text-muted">${notifications?.length || 0} thông báo mới</small>
                </div>
            `);

            // Render từng notification
            if (notifications?.length > 0) {
                notifications.forEach(function(notification) {
                    const timeDiff = timeSince(notification.created_at);
                    const notificationElement = `
                        <a href="${urlHome}/admin/thong-bao/edit/${notification.id}"
                           class="dropdown-item notification-item message-item-${notification.id}">
                            <div class="notification-content">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon">
                                        <i class="ti ti-bell text-primary"></i>
                                    </div>
                                    <div class="notification-text">
                                        <div class="notification-title">${notification.title}</div>
                                        <div class="notification-time">${timeDiff}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                    $messageBox.append(notificationElement);
                });
            } else {
                // Hiển thị thông báo khi không có notification
                $messageBox.append(`
                    <div class="notification-empty">
                        <div class="text-center py-4">
                            <i class="ti ti-bell-off text-muted"></i>
                            <p class="mt-2 mb-0">Không có thông báo mới</p>
                        </div>
                    </div>
                `);
            }

            // Thêm footer
            $messageBox.append('<div class="dropdown-divider mb-0"></div>');
            $messageBox.append(`
                <a href="{{ route('admin.notification.index') }}" class="dropdown-item text-center view-all">
                    Xem tất cả thông báo
                </a>
            `);
        }

        function reloadData() {
            const userId = getUserId();
            $.ajax({
                url: urlHome + '/admin/thong-bao/not-read-admin?admin_id=' + userId,
                type: 'GET',
                success: function(data) {
                    renderNotifications(data.notifications);
                },
                error: function(error) {
                    handleAjaxError(error)
                    console.error('Error fetching notifications:', error);
                }
            });
        }

        $('#message-box').on('hide.bs.dropdown', function() {
            updateNotificationStatus(getUserId());
        });

        function updateNotificationStatus(userId) {
            $.ajax({
                url: '{{ route('admin.notification.status') }}',
                type: 'POST',
                data: {
                    admin_id: userId
                },
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    console.log(response.success);
                    reloadData();
                },
                error: function(error) {
                    console.error('Error updating notification status:', error);
                }
            });
        }

        registerServiceWorker();
    });
</script>


<style>
    /* Badge styles */
    .badge-pulse {
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }

    /* Notification dropdown styles */
    .notification-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        background: rgba(30, 60, 114, 0.05);
    }

    .notification-content {
        width: 100%;
    }

    .notification-icon {
        width: 32px;
        height: 32px;
        border-radius: 16px;
        background: rgba(30, 60, 114, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    .notification-text {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        color: #2d3748;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .notification-time {
        color: #718096;
        font-size: 0.75rem;
    }

    .notification-empty {
        color: #718096;
        font-size: 0.875rem;
    }

    .notification-empty i {
        font-size: 1.5rem;
        opacity: 0.5;
    }

    .view-all {
        color: #1e3c72;
        font-weight: 500;
        padding: 0.75rem;
    }

    .view-all:hover {
        background: rgba(30, 60, 114, 0.05);
    }
</style>
