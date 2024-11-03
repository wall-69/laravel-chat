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
            :src="
                userChat.chat.type == 'dm'
                    ? asset(
                          userChat.chat.users[0].id == currentUser.id
                              ? userChat.chat.users[1].profile_picture
                              : userChat.chat.users[0].profile_picture
                      )
                    : asset(userChat.chat.picture)
            "
            :alt="
                (userChat.chat.type == 'dm'
                    ? asset(
                          userChat.chat.users[0].id == currentUser.id
                              ? userChat.chat.users[1].nickname
                              : userChat.chat.users[0].nickname
                      )
                    : asset(userChat.chat.name)) + ' profile picture'
            "
            class="bg-white rounded-circle"
            width="65"
            height="65"
            draggable="false"
        />

        <div class="d-flex flex-column min-w-0 placeholder-glow">
            <!-- Profile Name -->
            <p class="m-0 text-white fw-bold">
                {{
                    userChat.chat.type == "dm"
                        ? userChat.chat.users[0].id == currentUser.id
                            ? userChat.chat.users[1].nickname
                            : userChat.chat.users[0].nickname
                        : userChat.chat.name
                }}
            </p>
            <!-- Last message -->
            <p class="m-0 text-white fw-light small text-truncate">
                {{
                    lastMessage
                        ? lastMessage.type == "user"
                            ? lastMessage.user.nickname +
                              ": " +
                              lastMessage.content
                            : lastMessage.content
                        : "No messages sent yet."
                }}
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
