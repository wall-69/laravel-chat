<template>
    <div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
        <div
            v-if="currentChat"
            class="container-lg py-3 d-flex flex-column h-100"
        >
            <!-- Chat info -->
            <div
                class="container-lg border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between"
            >
                <div class="d-flex align-items-center gap-2">
                    <img
                        :src="asset(currentChat.picture)"
                        alt="X's profile picture"
                        class="bg-white rounded-circle"
                        height="45"
                        width="45"
                    />
                    <p class="m-0 text-white fw-bold">
                        {{ currentChat.name }}
                    </p>
                </div>
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

            <!-- Input -->
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
        <div
            v-else
            class="d-flex h-100 align-items-center justify-content-center"
        >
            <h3 class="text-center text-white">
                You don't have any chats. <br />
                Explore channels here:
                <a href="#" class="text-decoration-underline text-accent"
                    >Channels</a
                >!
            </h3>
        </div>
    </div>
</template>

<script setup>
import { asset } from "../helper";
import { inject, nextTick, onMounted, ref, watch } from "vue";

const emits = defineEmits(["loadingMessages", "loadedMessages"]);

const messages = ref([]);
const currentChat = inject("currentChat");
const currentUser = inject("currentUser");

const oldScrollTop = ref(0);
const oldScrollHeight = ref(0);
const loadingMessages = ref(false);
const page = ref(1);

// Send message form
const formMessage = ref("");

function sendMessage() {
    // route('messages.store', currentChat.chat_id)
    console.log("sendmessage");
}

// Echo
function joinPrivateChannel(channelName) {
    Echo.private(channelName).listen("MessageSent", (e) => {
        // i dont fucking know
    });
}

// Chat
const chatContainer = ref(null);

function scrollToBottom() {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
}

function switchChannels(oldChannel, newChannel) {
    Echo.leave(oldChannel);
    joinPrivateChannel(newChannel);
}

async function handleScroll() {
    if (
        chatContainer.value.scrollTop == 0 &&
        !loadingMessages.value &&
        page.value != -1
    ) {
        messages.value = [...(await getMessages()), ...messages.value];

        await nextTick();

        chatContainer.value.scrollTop =
            chatContainer.value.scrollHeight -
            oldScrollHeight.value +
            oldScrollTop.value;
    }
}

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

// Chat switch watcher
watch(
    () => currentChat.value,
    async (newChat, oldChat) => {
        if (newChat && newChat.id !== oldChat.id) {
            page.value = 1;

            messages.value = [];
            await nextTick();
            while (
                chatContainer.value.scrollHeight <=
                chatContainer.value.offsetHeight
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

            switchChannels(
                "chats." + oldChat.chat_id,
                "chats." + newChat.chat_id
            );
        }
    }
);
// Message loading/load watcher
watch(
    () => loadingMessages.value,
    (isLoading, wasLoading) => {
        emits(isLoading ? "loadingMessages" : "loadedMessages");
    }
);

onMounted(async () => {
    if (currentChat && currentChat.value) {
        // Load messages until chat is filled
        while (
            chatContainer.value.scrollHeight <=
                chatContainer.value.offsetHeight &&
            chatContainer.value.scrollTop == 0
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

        // Initial scroll
        nextTick(() => {
            scrollToBottom();
        });

        // Initial echo join
        joinPrivateChannel("chats." + currentChat.value.chat_id);
    }
});
</script>
