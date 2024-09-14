import "./bootstrap";
import * as bootstrap from "bootstrap";

// Vue
import { createApp } from "vue";
import ChatTab from "./components/ChatTab.vue";

const app = createApp();

app.component("chat-tab", ChatTab);

app.mount("#app");
