import { defineStore } from "pinia"
import notificationSound from '@/assests/notification.mp3';
import { onMessage } from "firebase/messaging";
import { messaging } from "@/firebase";
import {router, usePage} from "@inertiajs/vue3";
export const useNotificationStore = defineStore("notification", {
    state: () => ({
        // The active banner notification
        current: {
            visible: false,
            message: '',
            type: 'success', // success, error, info, warning
        },
        timeout: null,
        criticalAlerts: [], // Keeping your persistent alerts logic
        unreadCount: 0,
        notifications:[],
        initialized: false,
    }),

    actions: {
        // Core show logic
        show(message, type = 'success', duration = 10000) {
            if (this.timeout) clearTimeout(this.timeout);

            this.current.message = message;
            this.current.type = type;
            this.current.visible = true;

            if (duration > 0) {
                this.timeout = setTimeout(() => {
                    this.hide();
                }, duration);
            }
        },

        hide() {
            this.current.visible = false;
        },

        // Helper actions to match your previous API
        success(message) {
            this.show(message, 'success', 4000);
        },

        error(message, critical = false) {
            if (critical) {
                this.addCriticalAlert('error', message);
                this.show(message, 'error', 10000);
            } else {
                this.show(message, 'error', 6000);
            }
        },

        warning(message) {
            this.show(message, 'warning', 5000);
        },

        info(message) {
            this.show(message, 'info', 4000);
        },

        // Persistent alerts logic
        addCriticalAlert(type, message) {
            this.criticalAlerts.push({
                id: Date.now(),
                type,
                message,
                timestamp: new Date().toISOString()
            });
        },

        removeCriticalAlert(id) {
            this.criticalAlerts = this.criticalAlerts.filter(a => a.id !== id);
        },

        // Initial setup to get the count from Laravel
        setInitialCount(count) {
            this.unreadCount = count;
        },

        async fetchNotifications() {
            try {
                const response = await axios.get('/api/v1/notifications');
                this.notifications = response.data;
                this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                this.initialized = true;
            } catch (error) {
                console.error("Could not load notifications from DB", error);
            }
        },

        //Listen for Push Notification
        listenForPushNotifications() {
            onMessage(messaging, (notification) => {

                const data = notification?.data || notification?.notification || {};

                const newItem = {
                    id: data.id || Math.random(),
                    data,
                    read_at: null,
                    created_at: new Date().toISOString()
                };

                this.notifications.unshift(newItem);
                this.unreadCount++;

                // Sound (same as Echo)
                const audio = new Audio(notificationSound);
                audio.play().catch(() => {});

                let actionPrefix = '📄';

                switch (data.action_type) {
                    case 'file_routed': actionPrefix = '📂 [Transfer]'; break;
                    case 'file_returned': actionPrefix = '↩️ [Returned]'; break;
                    case 'new_submission': actionPrefix = '📄 [New File]'; break;
                }

                const fullMessage = `${actionPrefix} ${data.message}`;

                // UI behavior (same logic as Echo)
                if (data.priority === 'immediate') {
                    this.addCriticalAlert('error', `IMMEDIATE: ${fullMessage}`);
                    this.error(fullMessage, true);
                }
                else if (data.priority === 'urgent') {
                    this.warning(fullMessage);
                }
                else if (data.priority === 'normal') {
                    this.success(fullMessage);
                }
                else {
                    data.action_type === 'file_returned'
                        ? this.warning(fullMessage)
                        : this.success(fullMessage);
                }

                // Optional: system notification (ONLY for background-like UX)
                if (document.visibilityState !== 'visible' && Notification.permission === 'granted') {
                    new Notification('New Notification', {
                        body: fullMessage,
                        icon: '/icon.png'
                    });
                }
            });
        },

        // Real-time listener
        listenForNotifications(userId) {
            if (!userId) return;

            const userChannel = window.Echo.private(`user.${userId}`);


            // Existing Notification Listener
            userChannel.notification((notification) => {
                this.handleIncomingNotification(notification);
            });

            // Silent File Refresh
            userChannel.listen('.file.sent', (e) => {
                const page = usePage();
                // if (e.file && page.props.files?.data) {
                //     page.props.files.data.unshift(e.file);
                // }
                // This only fetches the 'files' data from the server
                router.reload({
                    only: ['files'],
                    preserveScroll: true,
                    preserveState: true
                });
            });

            // userChannel.listen('.FileAssigned', (e) => {
            //     const page = usePage();
            //     // This only fetches the 'files' data from the server
            //     router.reload({
            //         only: ['stats', 'tableData', 'recentActivity', 'overdueFiles'],
            //         preserveScroll: true
            //     });
            // });
        },
            // You can still keep your public channel listeners here too
            // window.Echo.channel("clinical-alerts")
            //     .listen("CriticalLabResult", (e) => {
            //         this.error(`Critical Lab Result: ${e.patient.name}`, true);
            //     });
            //},

        // Stop listening when user logs out (prevents memory leaks)
        stopListening(userId) {
            if (window.Echo) {
                window.Echo.leave(`App.Models.User.${userId}`);
            }
        },

        addNotification(notification) {
            // Add to the top of the list
            this.notifications.unshift(notification);
            this.unreadCount++;

            // Trigger a browser toast if you have a toast library
            // this.show(notification.data.message);
        },
        async markAsRead(id) {
            // Update locally
            const n = this.notifications.find(n => n.id === id);
            if (n && !n.read_at) {
                n.read_at = new Date();
                this.unreadCount--;
                // Optional: Call your Laravel API to mark as read in DB
                try {
                 await axios.patch(`/api/v1/notifications/${id}/read`);
                } catch (error) {
                    console.error("Failed to sync read status to server:", error);
                }
            }
        },
        async markAllAsRead() {
            // Update all locally
            this.notifications.forEach(n => {
                if (!n.read_at) n.read_at = new Date().toISOString();
            });
            this.unreadCount = 0;

            try {
                await axios.post('/api/v1/notifications/mark-all-read');
            } catch (error) {
                console.error("Failed to mark all as read:", error);
            }
        },
        handleIncomingNotification(notification) {
            const newItem = {
                id: notification.id || Math.random(),
                data: notification,
                read_at: null,
                created_at: new Date().toISOString()
            };

            this.notifications.unshift(newItem);
            this.unreadCount++;

            const audio = new Audio(notificationSound);
            audio.play().catch(() => {});

            let actionPrefix = '📄';

            switch (notification.action_type) {
                case 'file_routed': actionPrefix = '📂 [Transfer]'; break;
                case 'file_returned': actionPrefix = '↩️ [Returned]'; break;
                case 'new_submission': actionPrefix = '📄 [New File]'; break;
            }

            const fullMessage = `${actionPrefix} ${notification.message}`;

            if (notification.priority === 'immediate') {
                this.addCriticalAlert('error', `IMMEDIATE: ${fullMessage}`);
                this.error(fullMessage, true);
            }
            else if (notification.priority === 'urgent') {
                this.warning(fullMessage);
            }
            else {
                notification.action_type === 'file_returned'
                    ? this.warning(fullMessage)
                    : this.success(fullMessage);
            }
        }
    }
});






