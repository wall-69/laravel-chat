<template>
    <div
        role="button"
        @click="$emit('switchChat', userChat.id)"
        class="d-flex gap-2 m-0 p-2 border-bottom border-divider text-decoration-none user-select-none"
        :class="{
            'bg-read-chat-tab': type == 'read',
            'bg-unread-chat-tab': type == 'unread',
        }"
    >
        <!-- Chat Picture -->
        <img
            :src="asset(userChat.picture)"
            :alt="userChat.name + ' chat profile picture'"
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
import { onMounted, ref } from "vue";
import { asset } from "../helper";

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

/*
 *  EVENTS
 */

onMounted(async () => {
    lastMessage.value = await getLastMessage();
});

/*
 *  COMPONENT
 */

// Last message
const lastMessage = ref("");

/**
 * Gets the last message from the chat.
 * @returns The last message formatted (nickname: message), if one exists, otherwise "No messages sent yet.".
 */
async function getLastMessage() {
    try {
        const res = await axios.get(
            "/chat/" + props.userChat.chat_id + "/last-message"
        );

        if (res.data.lastMessage) {
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
</script>
