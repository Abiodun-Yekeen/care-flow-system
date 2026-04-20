import { getToken } from "firebase/messaging";
import { messaging } from "@/firebase.js";
import axios from "axios";

export async function registerForPush() {
    const permission = await Notification.requestPermission();

    if (permission !== "granted") return;

    const token = await getToken(messaging, {
        vapidKey: "BHDmg2gudvL2gggZ2wqS3MGFNUQGzu2-_ZceXB_ZZg02i8F0pe-kGz1vMRbQYPc-h-D1V0GHnjU9qJJb_7-muxA"
    });

    await axios.post("/api/v1/fcm-token", { token });
    return token;
}
