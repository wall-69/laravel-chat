<template>
    <div
        role="button"
        @click="switchChat(userChat.id)"
        class="d-flex gap-2 m-0 p-2 border-bottom border-divider text-decoration-none user-select-none"
        :class="{
            'bg-read-chat-tab': type == 'read',
            'bg-unread-chat-tab': type == 'unread',
        }"
    >
        <!-- Chat Picture -->
        <img
            :src="asset(userChat.picture)"
            alt="X's profile picture"
            class="bg-white rounded-circle"
            width="65"
            height="65"
            draggable="false"
        />

        <div class="d-flex flex-column mw-0 placeholder-glow">
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

// Props
const props = defineProps({
    userChat: Object,
    type: String,
    chatPicture: String,
});

// Emits
const emits = defineEmits(["switchChat"]);

// Last message
const lastMessage = ref("");

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

function switchChat(id) {
    emits("switchChat", id);
}

// Events
onMounted(async () => {
    lastMessage.value = await getLastMessage();
});
</script>
