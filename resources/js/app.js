import "./bootstrap";
import * as bootstrap from "bootstrap";

// Vue
import { createApp } from "vue";
import { ZiggyVue } from "ziggy-js";
import Chat from "./components/Chat.vue";
import ChatTab from "./components/ChatTab.vue";
import ChatContainer from "./components/ChatContainer.vue";
import ChatSentMessage from "./components/ChatSentMessage.vue";
import ChatReceivedMessage from "./components/ChatReceivedMessage.vue";

const app = createApp();
app.use(ZiggyVue);

app.component("chat", Chat);
app.component("chat-tab", ChatTab);
app.component("chat-container", ChatContainer);
app.component("chat-sent-message", ChatSentMessage);
app.component("chat-received-message", ChatReceivedMessage);

app.mount("#app");
