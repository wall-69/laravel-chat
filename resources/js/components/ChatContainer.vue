<template>
    <div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
        <!-- IF currentChat is not null, render it -->
        <div
            v-if="currentChat"
            class="container-lg py-3 d-flex flex-column h-100"
        >
            <!-- Chat info -->
            <div
                class="container-lg border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between"
            >
                <div class="d-flex align-items-center gap-2">
                    <!-- Chat picture -->
                    <img
                        :src="asset(currentChat.picture)"
                        :alt="currentChat.name + ' chat profile picture'"
                        class="bg-white rounded-circle"
                        height="45"
                        width="45"
                    />
                    <!-- Chat name -->
                    <p class="m-0 text-white fw-bold">
                        {{ currentChat.name }}
                    </p>
                </div>
                <!-- Chat actions button -->
                <button class="border-0 bg-transparent text-white ms-auto">
                    <i class="bx bx-dots-horizontal-rounded bx-md"></i>
                </button>
            </div>

            <!-- Chat -->
            <div
                ref="chatContainer"
                @scroll="handleScroll"
                class="d-flex flex-column py-2 overflow-y-scroll px-3"
                :class="{
                    'mt-auto': messages.length > 0,
                }"
            >
                <!-- Loading spinner -->
                <div
                    v-show="messages.length == 0 || loadingMessages"
                    class="spinner-border text-white mx-auto"
                    :class="{
                        'mt-5': messages.length == 0,
                        'p-2-5': loadingMessages,
                    }"
                    role="status"
                >
                    <span class="visually-hidden">Loading...</span>
                </div>

                <!-- Messages -->
                <template v-show="messages" v-for="message in messages">
                    <!-- IF message.user_id == currentUser.id -->
                    <chat-sent-message
                        v-if="message.user_id == currentUser.id"
                        :message="message.content"
                    ></chat-sent-message>

                    <!-- ELSE -->
                    <chat-received-message
                        v-else
                        :user="message.user"
                        :message="message.content"
                    ></chat-received-message>
                </template>
            </div>

            <!-- Send message form -->
            <form
                @submit.prevent="sendMessage"
                class="input-group shadow rounded-5"
                :class="{
                    'mt-auto': messages.length == 0,
                }"
                method="POST"
            >
                <input
                    v-model="formMessage"
                    type="text"
                    name="message"
                    class="form-control py-2 border border-start-0 border-divider border-opacity-25 bg-primary text-white rounded-start-4"
                    required
                />

                <button
                    type="submit"
                    class="btn btn-accent text-white fw-bold border border-divider border-opacity-25 rounded-end-4 px-4"
                >
                    Send
                </button>
            </form>
        </div>

        <!-- ELSE render no chat -->
        <div
            v-else
            class="d-flex h-100 align-items-center justify-content-center"
        >
            <h3 class="text-center text-white">
                You don't have any chats.
                <br />
                Explore channels here:
                <a href="#" class="text-decoration-underline text-accent">
                    Channels
                </a>
                !
            </h3>
        </div>
    </div>
</template>

<script setup>
import useEmitter, { asset } from "../helper";
import { inject, nextTick, onMounted, ref, watch } from "vue";

/*
 * EMITS
 */

const emits = defineEmits(["loadingMessages", "loadedMessages"]);
const emitter = useEmitter();

emitter.on("messageSent", async (message) => {
    // Dont add message to container if it is from another chat
    if (message.chat_id != currentChat.value.chat_id) {
        return;
    }

    // Add the message to current messages array
    messages.value.push(message);

    // Wait one tick for the message to render
    // Then scroll to bottom
    await nextTick();
    scrollToBottom();
});

/*
 *  EVENTS
 */
onMounted(() => {
    if (currentChat && currentChat.value) {
        // Prepare chat by loading messages and scrolling to bottom
        prepareChat();
    }
});

/*
 * COMPONENT
 */

const messages = ref([]);
const currentChat = inject("currentChat");
const currentUser = inject("currentUser");

// Scroll message loading variables
const oldScrollTop = ref(0);
const oldScrollHeight = ref(0);
const loadingMessages = ref(false);
const page = ref(1);

// Send message form
const formMessage = ref("");
/**
 * Sends a POST request to store a new message in the current chat.
 */
function sendMessage() {
    try {
        axios.post(
            route("messages.store", { chatId: currentChat.value.chat_id }),
            {
                message: formMessage.value,
            }
        );

        // Reset the message input
        formMessage.value = "";
    } catch (error) {
        console.error("Send message request error: " + error);
    }
}

// Chat
const chatContainer = ref(null);

/**
 * Scrolls to the bottom of the ChatContainer
 */
function scrollToBottom() {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
}

/**
 * Handles the scroll of the chat.
 * If the user scrolls to the top of the ChatContainer, older messages will be loaded, only if the current page is not -1 (=> no more messages can be loaded).
 */
async function handleScroll() {
    // If the scroll is at top, no messages are currently loading and the page is not -1
    if (
        chatContainer.value.scrollTop == 0 &&
        !loadingMessages.value &&
        page.value != -1
    ) {
        // Add the new messages to the array
        messages.value = [...(await getMessages()), ...messages.value];

        // Await one tick for the container to update with the new messages
        await nextTick();

        // Scroll to previous position before loading messages (to the message that the user last seen)
        chatContainer.value.scrollTop =
            chatContainer.value.scrollHeight -
            oldScrollHeight.value +
            oldScrollTop.value;
    }
}

/**
 * Gets the messages from the current page with get request. Also sets the `oldScrollTop` and `oldScrollHeight` values and sets `loadingMessages` during the process.
 * @returns The array of the messages from current page (empty if there are no more messages to load)
 */
async function getMessages() {
    try {
        loadingMessages.value = true;

        const res = await axios.get("/messages/" + currentChat.value.chat_id, {
            params: { page: page.value },
        });
        page.value += 1;

        oldScrollTop.value = chatContainer.value.scrollTop;
        oldScrollHeight.value = chatContainer.value.scrollHeight;

        const messages = res.data.messages;

        if (messages.length == 0) {
            page.value = -1;
            return [];
        }
        return messages;
    } catch (error) {
        console.error("Get messages request error: " + error);
    } finally {
        loadingMessages.value = false;
    }
}

/**
 * "Prepares" the chat by loading messages until screen is filled and then scrolls to bottom of the ChatContainer.
 */
async function prepareChat() {
    while (
        chatContainer.value.scrollHeight <= chatContainer.value.offsetHeight
    ) {
        if (page.value == -1) {
            break;
        }
        if (loadingMessages.value) {
            return;
        }

        messages.value = [...(await getMessages()), ...messages.value];
        await nextTick();
    }

    nextTick(() => {
        scrollToBottom();
    });
}

/*
 *  WATCHERS
 */

// Chat switch watcher - switches to new chat, when the currentChat is changed.
watch(
    () => currentChat.value,
    async (newChat, oldChat) => {
        if (newChat && newChat.id !== oldChat.id) {
            // Reset current page and set messages to empty array, then wait one tick to empty the chat visually
            page.value = 1;
            messages.value = [];
            await nextTick();

            // Prepare the chat by loading messages and then scrolling to bottom
            prepareChat();
        }
    }
);

// Message loading/load watcher - emits the apropriate emit based on the current loading state
watch(
    () => loadingMessages.value,
    (isLoading, wasLoading) => {
        emits(isLoading ? "loadingMessages" : "loadedMessages");
    }
);
</script>
