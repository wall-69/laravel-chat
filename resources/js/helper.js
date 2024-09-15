import axios from "axios";

const BASE_URL = "http://127.0.0.1:8000";

export function asset(url) {
    return BASE_URL + "/" + url;
}

export async function csrf() {
    try {
        await axios.get("/sanctum/csrf-cookie");
    } catch (error) {
        console.error("CSRF Token request error: " + error);
    }
}
