<template>
    <div
        role="button"
        @click="handleClick()"
        class="d-flex gap-2 m-0 p-2 border-bottom border-divider text-decoration-none user-select-none"
        :class="{
            'bg-read-chat-tab': type == 'read',
            'bg-unread-chat-tab': type == 'unread',
        }"
    >
        <!-- Chat Picture -->
        <img
            :src="asset(userChat.picture)"
            :alt="userChat.name + ' profile picture'"
            class="bg-white rounded-circle"
            width="65"
            height="65"
            draggable="false"
        />

        <div class="d-flex flex-column min-w-0 placeholder-glow">
            <!-- Profile Name -->
            <p class="m-0 text-white fw-bold">
                {{ userChat.name }}
            </p>
            <!-- Last message -->
            <p
                class="m-0 text-white fw-light small text-truncate"
                :class="{
                    placeholder: !lastMessage,
                }"
            >
                {{ lastMessage }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { inject, onBeforeUnmount, onMounted, ref } from "vue";
import { asset, useEmitter } from "../helper";

/*
 *  PROPS
 */

const props = defineProps({
    userChat: Object,
    type: String,
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
        type.value = "unread";
    }

    // Update lastMessage and lastMessageTime
    lastMessage.value = message.user.nickname + ": " + message.content;
    lastMessageTime.value = new Date();
});

/*
 *  EVENTS
 */

onMounted(async () => {
    // Get the last message from the chat
    lastMessage.value = await getLastMessage();

    // If the last message was sent after the
    if (
        lastMessage.value.user_id != currentUser.id &&
        (lastMessageTime.value > new Date(userChat.value.last_read) ||
            userChat.value.last_read == null)
    ) {
        type.value = "unread";
    }
});

onBeforeUnmount(() => {
    updateLastRead();
});

/*
 *  COMPONENT
 */

const type = ref(props.type);
const userChat = ref(props.userChat);

const currentUser = inject("currentUser");
const currentChat = inject("currentChat");

// Last message
const lastMessage = ref("");
const lastMessageTime = ref(new Date());

/**
 * Gets the last message from the chat.
 * @returns The last message formatted (nickname: message), if one exists, otherwise "No messages sent yet.".
 */
async function getLastMessage() {
    try {
        const res = await axios.get(
            "/chat/" + userChat.value.chat_id + "/last-message"
        );

        if (res.data.lastMessage) {
            lastMessageTime.value = new Date(res.data.lastMessage.created_at);

            return (
                res.data.lastMessage.nickname +
                ": " +
                res.data.lastMessage.content
            );
        }
    } catch (error) {
        console.error("Last message request error: " + error);
    }

    return "No messages sent yet.";
}

/**
 * Handles the click on the ChatTab. Emits `switchChat` to parent and updates ChatTab type.
 */
async function handleClick() {
    emits("switchChat", userChat.value.id);

    if (type.value == "unread") {
        updateLastRead();
    }
}

/**
 * Makes a post request to update the last read
 */
async function updateLastRead() {
    try {
        await axios.post("/user-chat/" + userChat.value.id + "/last-read");
    } catch (error) {
        console.error("Last read update request error: " + error);
    } finally {
        type.value = "read";
    }
}
</script>
