import { defineStore } from "pinia"

export const useNotificationStore = defineStore("notification", {
    state: () => ({
        // The active banner notification
        current: {
            visible: false,
            message: '',
            type: 'success', // success, error, info, warning
        },
        timeout: null,
        criticalAlerts: [] // Keeping your persistent alerts logic
    }),

    actions: {
        // Core show logic
        show(message, type = 'success', duration = 3600) {
            if (this.timeout) clearTimeout(this.timeout);

            this.current.message = message;
            this.current.type = type;
            this.current.visible = true;

            // if (duration > 0) {
            //     this.timeout = setTimeout(() => {
            //         this.hide();
            //     }, duration);
            // }
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
        // Real-time listener
        // listen() {
        //     echo.channel("clinical-alerts")
        //         .listen("CriticalLabResult", (e) => {
        //             // Critical - use both systems
        //             this.error(
        //                 `Critical Lab Result: ${e.patient.name} - ${e.test}`,
        //                 true // critical flag
        //             )
        //         })
        //         .listen("AppointmentReminder", (e) => {
        //             // Non-critical - just toast
        //             this.info(`Appointment: ${e.patient.name} at ${e.time}`)
        //         })
        //         .listen("LowInventory", (e) => {
        //             // Warning - use toast
        //             this.warning(`Low inventory: ${e.item} (${e.quantity} left)`)
        //         })
        // }
    }
});






