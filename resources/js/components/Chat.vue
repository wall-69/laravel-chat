<template>
    <div class="px-2 px-lg-0 container-lg py-5 h-100">
        <div class="row h-100 gx-0 gx-lg-5">
            <div
                class="d-none d-lg-block col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll"
            >
                <chat-tab
                    v-for="userChat in userChats"
                    :key="userChat.id"
                    :user-chat="userChat"
                    type="read"
                    :chat-picture="asset(userChat.picture)"
                >
                </chat-tab>
            </div>
            <div id="chat" class="col-12 col-lg-9 mh-100">
                <!-- <x-chat :userChat="$currentChat" /> -->
                <chat-container :user-chat="currentChat"></chat-container>
            </div>
        </div>
    </div>
</template>

<script setup>
import { asset } from "../helper";

const props = defineProps({
    currentChat: Object,
    userChats: Array,
});
</script>
