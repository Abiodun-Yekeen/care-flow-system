import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: "AIzaSyB3HpR6J8Qyz_28zFHtka-xGcM3H2J3otE",
    authDomain: "careflow-83b22.firebaseapp.com",
    projectId: "careflow-83b22",
    storageBucket: "careflow-83b22.firebasestorage.app",
    messagingSenderId: "728170256522",
    appId: "1:728170256522:web:c060417f56ed0bc5e2b573",
    measurementId: "G-T304053JD0"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

export { messaging, getToken, onMessage };

