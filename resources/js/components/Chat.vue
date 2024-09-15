<template>
    <div class="px-2 px-lg-0 container-lg py-5 h-100">
        <div class="row h-100 gx-0 gx-lg-5">
            <div
                class="d-none d-lg-block col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll"
            >
                <chat-tab
                    v-for="userChat in userChats"
                    @switch-chat="handleSwitchChat"
                    :key="userChat.id"
                    :user-chat="userChat"
                    type="read"
                >
                </chat-tab>
            </div>
            <div id="chat" class="col-12 col-lg-9 mh-100">
                <chat-container></chat-container>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, provide, ref } from "vue";
import { csrf } from "../helper";

const props = defineProps({
    currentUser: Object,
    currentChat: Object,
    userChats: Array,
});

const currentChat = ref(props.currentChat);

provide("currentUser", props.currentUser);
provide("currentChat", currentChat);

async function getUserChat(id) {
    try {
        await csrf();

        const res = await axios.get("/chat/" + id);

        return res.data.userChat;
    } catch (error) {
        console.error("Get chat request error: " + error);
    }
}

async function handleSwitchChat(id) {
    currentChat.value = await getUserChat(id);
}
</script>
