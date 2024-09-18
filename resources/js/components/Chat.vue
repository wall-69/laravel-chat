<template>
    <div class="px-2 px-lg-0 container-lg py-5">
        <div class="row gx-0 gx-lg-5">
            <div
                class="d-none d-lg-block col-3 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll"
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
            <div id="chat" class="col-12 col-lg-9 vh-100">
                <chat-container
                    @loading-messages="handleLoadingMessages"
                    @loaded-messages="handleLoadedMessages"
                ></chat-container>
            </div>
        </div>
    </div>
</template>

<script setup>
import { provide, ref } from "vue";

/*
 *  PROPS
 */

const props = defineProps({
    currentUser: Object,
    currentChat: Object,
    userChats: Array,
});

/*
 *  COMPONENT
 */

const currentChat = ref(props.currentChat);

provide("currentUser", props.currentUser);
provide("currentChat", currentChat);

// Loading messages logic (because I am too dumb to do it any other way rn)
const loadingMessages = ref(false);

/**
 * When loadingMessages is emitted from ChatContainer, set loadingMessages to true.
 */
function handleLoadingMessages() {
    loadingMessages.value = true;
}

/**
 * When loadedMessages is emitted from ChatContainer, set loadingMessages to false.
 */
function handleLoadedMessages() {
    loadingMessages.value = false;
}

/**
 * When switchChat is emitted from ChatContainer, try to change the currentChat to the one specified, if it is valid.
 * @param id the UserChat id
 */
function handleSwitchChat(id) {
    // Dont allow chat switch when loading messages
    if (loadingMessages.value) {
        return;
    }

    // Dont load already loaded chat
    if (currentChat.value.id == id) {
        return;
    }

    // Find the new user chat based on the id provided
    const newUserChat = props.userChats.find((userChat) => {
        return userChat.id == id;
    });

    // If id is invalid, throw error
    if (newUserChat) {
        currentChat.value = newUserChat;
    } else {
        console.error("Invalid user chat id in handleSwitchChat.");
    }
}
</script>
