import axios from "axios";

export async function csrf() {
    try {
        await axios.get("/sanctum/csrf-cookie");
    } catch (error) {
        console.error("CSRF Token request error: " + error);
    }
}
