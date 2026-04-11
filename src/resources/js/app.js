import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import { useInertiaSync } from "@/Composables/auth/useInertiaSync.js";

const pinia = createPinia();
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Initialize sync logic
        const { sync } = useInertiaSync();

        // Create the Vue app
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)

        // Initialize sync
        sync();

        // Mount the app
        app.mount(el);

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});






// import '../css/app.css';
// import './bootstrap';
//
// import { createInertiaApp } from '@inertiajs/vue3';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createApp, h } from 'vue';
// import { ZiggyVue } from 'ziggy-js';
// import { createPinia } from 'pinia';
// import Toast from "vue-toastification";
// import "vue-toastification/dist/index.css";
// import {useInertiaSync} from "@/Composables/auth/useInertiaSync.js";
//
// const pinia = createPinia()
// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
//
// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) =>
//         resolvePageComponent(
//             `./Pages/${name}.vue`,
//             import.meta.glob('./Pages/**/*.vue'),
//         ),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .use(pinia)
//             .use(Toast, {
//                 position: "top-right",
//                 timeout: 7000,
//                 containerClassName: "my-toast-container",
//             })
//         // Initialize the sync logic
//         const { sync } = useInertiaSync()
//         sync()
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });
