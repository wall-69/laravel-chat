<template>
    <div
        role="button"
        @click="handleClick()"
        class="d-flex gap-2 m-0 p-2 border-bottom border-divider text-decoration-none user-select-none"
        :class="{
            'bg-read-chat-tab': read,
            'bg-unread-chat-tab': !read,
        }"
    >
        <!-- Chat Picture -->
        <img
            :src="getChatPicture"
            :alt="
                getChatName + ' ' + (isDM ? 'profile' : 'channel') + ' picture'
            "
            class="bg-white rounded-circle ratio-1"
            width="65"
            height="65"
            draggable="false"
        />

        <div class="d-flex flex-column min-w-0 placeholder-glow">
            <!-- Profile Name -->
            <p class="m-0 text-white fw-bold">
                {{ getChatName }}
            </p>
            <!-- Last message -->
            <p class="m-0 text-white fw-light small text-truncate">
                {{ getLastMessage }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed, inject, onBeforeUnmount, onMounted, ref } from "vue";
import { asset, useEmitter } from "../helper";

/*
 *  PROPS
 */

const props = defineProps({
    userChat: Object,
});

/*
 *  EMITS
 */

const emits = defineEmits(["switchChat"]);
const emitter = useEmitter();

emitter.on("messageSent", async (message) => {
    // Check if the message that was sent is from this ChatTab's UserChat
    if (message.chat_id != userChat.value.chat_id) {
        return;
    }

    // If the message is not sent in currently opened chat and it was sent by another user, the ChatTab will be marked as unread
    if (
        message.chat_id != currentChat.value.chat_id &&
        message.user_id != currentUser.id
    ) {
        read.value = false;
    }

    // Update lastMessage
    lastMessage.value = message;
});

emitter.on("chatDeleted", (chatId) => {
    // Check if the message that was sent is from this ChatTab's UserChat
    if (chatId != userChat.value.chat_id) {
        return;
    }

    // Set the chatDeleted flag to true
    chatDeleted.value = true;
});

/*
 *  EVENTS
 */

onMounted(() => {
    // If the last message was sent after the last read, set the ChatTab as unread
    if (
        lastMessage.value &&
        lastMessage.value.user_id != currentUser.id &&
        (new Date(lastMessage.value.created_at) >
            new Date(userChat.value.last_read) ||
            userChat.value.last_read == null)
    ) {
        read.value = false;
    }
});

onBeforeUnmount(() => {
    // Update the last read value of the current user chat when the chat is closed. (This is only assumed that the chat tab is unmounted when chat is closed.)
    updateLastRead();
});

/*
 *  COMPONENT
 */

const read = ref(true);
const userChat = ref(props.userChat);

const currentUser = inject("currentUser");
const currentChat = inject("currentChat");

const isDM = computed(() => userChat.value.chat.type == "dm");
const getLastMessage = computed(() => {
    // No message
    if (!lastMessage.value) {
        return "No messages sent yet.";
    }

    // Non user message (system message)
    if (lastMessage.value.type != "user") {
        return lastMessage.value.content;
    }

    // User message (nickname: message)
    return lastMessage.user.nickname + ": " + lastMessage.value.content;
});
const getChatName = computed(() => {
    if (!isDM.value) {
        return userChat.value.chat.name;
    }

    if (userChat.value.chat.users[0].id == currentUser.id) {
        return userChat.value.chat.users[1].nickname;
    } else {
        return userChat.value.chat.users[0].nickname;
    }
});
const getChatPicture = computed(() => {
    if (!isDM.value) {
        return asset(userChat.value.chat.picture);
    }

    if (userChat.value.chat.users[0].id == currentUser.id) {
        return userChat.value.chat.users[1].profile_picture;
    } else {
        return userChat.value.chat.users[0].profile_picture;
    }
});

const chatDeleted = ref(false);

// Last message
const lastMessage = ref(userChat.value.chat.last_message);

/**
 * Handles the click on the ChatTab. Emits `switchChat` to parent and updates ChatTab read status.
 */
async function handleClick() {
    emits("switchChat", userChat.value.id);

    if (!read.value) {
        updateLastRead();
    }
}

/**
 * Makes a post request to update the last read, then sets `read` variable to true.
 */
async function updateLastRead() {
    // Check, if the ChatTab was deleted (so was the UserChat also)
    if (chatDeleted.value) {
        return;
    }

    try {
        await axios.post(
            route("userChats.updateLastRead", { userChat: userChat.value.id })
        );
    } catch (error) {
        console.error("Last read update request error: " + error);
    } finally {
        read.value = true;
    }
}
</script>
