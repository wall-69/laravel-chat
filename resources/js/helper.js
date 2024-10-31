import { getCurrentInstance } from "vue";

const LOCAL_URL = "http://127.0.0.1:8000";
const PROD_URL = "http://192.168.1.220:8000";
const BASE_URL = PROD_URL;

export function asset(url) {
    return BASE_URL + "/" + url;
}

export function useEmitter() {
    const internalInstance = getCurrentInstance();
    const emitter = internalInstance.appContext.config.globalProperties.emitter;

    return emitter;
}
