<template>
    <div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
        <!-- IF currentChat is not null, render it -->
        <div
            v-if="currentChat"
            class="container-lg py-3 d-flex flex-column h-100 position-relative"
        >
            <!-- Chat info -->
            <div
                class="container-lg border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between"
            >
                <div class="d-flex align-items-center gap-2">
                    <!-- Chat picture -->
                    <img
                        :src="
                            currentChat.chat.type == 'dm'
                                ? asset(
                                      currentChat.chat.users[0].id ==
                                          currentUser.id
                                          ? currentChat.chat.users[1]
                                                .profile_picture
                                          : currentChat.chat.users[0]
                                                .profile_picture
                                  )
                                : asset(currentChat.chat.picture)
                        "
                        :alt="
                            (currentChat.chat.type == 'dm'
                                ? currentChat.chat.users[0].id == currentUser.id
                                    ? currentChat.chat.users[1].nickname
                                    : currentChat.chat.users[0].nickname
                                : currentChat.chat.name) + ' profile picture'
                        "
                        class="bg-white rounded-circle"
                        height="45"
                        width="45"
                    />
                    <!-- Chat name -->
                    <component
                        :is="currentChat.chat.type == 'dm' ? 'a' : 'p'"
                        :href="
                            '/profile/' +
                            (currentChat.chat.type == 'dm'
                                ? currentChat.chat.users[0].id == currentUser.id
                                    ? currentChat.chat.users[1].nickname
                                    : currentChat.chat.users[0].nickname
                                : currentChat.chat.name)
                        "
                        class="m-0 text-white fw-bold"
                    >
                        {{
                            currentChat.chat.type == "dm"
                                ? currentChat.chat.users[0].id == currentUser.id
                                    ? currentChat.chat.users[1].nickname
                                    : currentChat.chat.users[0].nickname
                                : currentChat.chat.name
                        }}
                    </component>
                </div>

                <!-- Chat actions button -->
                <button
                    @click="toggleActionsShown"
                    class="border-0 bg-transparent text-white ms-auto"
                >
                    <i class="bx bx-dots-horizontal-rounded bx-md"></i>
                </button>
            </div>

            <!-- Chat actions -->
            <div
                ref="chatActions"
                @touchstart.stop
                class="bg-white p-3 rounded-3 position-absolute end-0 d-flex flex-column align-items-start overflow-y-scroll w-100 max-h-75"
                :class="{
                    'd-none': !actionsShown,
                }"
            >
                <!-- CHAT NAME -->
                <span class="text-center fw-bold">
                    {{
                        currentChat.chat.type == "dm"
                            ? currentChat.chat.users[0].id == currentUser.id
                                ? currentChat.chat.users[1].nickname
                                : currentChat.chat.users[0].nickname
                            : currentChat.chat.name
                    }}
                </span>
                <hr class="w-100" />
                <!-- USERS LIST -->
                <h6>Users ({{ currentChat.chat.users.length }}):</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li
                        v-for="user in currentChat.chat.users"
                        class="d-flex align-items-center gap-2"
                    >
                        <!-- USER PROFILE PICTURE -->
                        <img
                            :src="asset(user.profile_picture)"
                            :alt="user.nickname + ' profile picture'"
                            class="rounded-circle"
                            height="45"
                            width="45"
                        />
                        <!-- USER NICKNAME -->
                        <a :href="route('users.show', user.nickname)">
                            {{ user.nickname }}
                            {{
                                currentChat.chat.type == "channel" &&
                                user.id == currentChat.chat.admin.user_id
                                    ? "(Admin)"
                                    : ""
                            }}
                        </a>
                        <!-- ADMIN KICK/BAN BUTTONS -->
                        <template
                            v-if="
                                currentChat.chat.type == 'channel' &&
                                currentUser.id ==
                                    currentChat.chat.admin.user_id &&
                                user.id != currentUser.id
                            "
                        >
                            <button
                                @click="handleKick(user.id)"
                                class="btn btn-warning p-1"
                            >
                                Kick
                            </button>
                            <button
                                @click="handleBan(user.id)"
                                class="btn btn-danger p-1"
                            >
                                Ban
                            </button>
                        </template>
                    </li>
                </ul>
                <hr class="w-100" />
                <!-- ADMIN ACTIONS & BANNED USERS -->
                <template
                    v-if="
                        currentChat.chat.type == 'channel' &&
                        currentUser.id == currentChat.chat.admin.user_id
                    "
                >
                    <!-- Rename -->
                    <chat-admin-form
                        :chat="currentChat.chat"
                        action-name="Rename"
                        method="PATCH"
                    >
                        <input
                            type="text"
                            name="name"
                            :value="currentChat.chat.name"
                        />
                    </chat-admin-form>
                    <!-- Change visibility -->
                    <chat-admin-form
                        :chat="currentChat.chat"
                        action-name="Change visibility"
                        method="PATCH"
                    >
                        <select name="is_private">
                            <option
                                value="0"
                                :selected="currentChat.chat.is_private == 0"
                            >
                                Public
                            </option>
                            <option
                                value="1"
                                :selected="currentChat.chat.is_private == 1"
                            >
                                Private
                            </option>
                        </select>
                    </chat-admin-form>
                    <!-- Change picture -->
                    <chat-admin-form
                        :chat="currentChat.chat"
                        action-name="Change picture"
                        method="PATCH"
                        enctype="multipart/form-data"
                    >
                        <input
                            type="file"
                            name="chat_picture"
                            accept="image/*"
                        />
                    </chat-admin-form>
                    <!-- Change admin -->
                    <chat-admin-form
                        :chat="currentChat.chat"
                        action-name="Change admin"
                        :action-url="
                            route('chatAdmins.changeAdmin', {
                                chat: currentChat.chat.id,
                            })
                        "
                        method="POST"
                    >
                        <select name="new_user_id">
                            <option
                                v-for="user in currentChat.chat.users"
                                :value="user.id"
                                :selected="user.id == currentUser.id"
                            >
                                {{ user.nickname }}
                            </option>
                        </select>
                    </chat-admin-form>
                    <!-- Delete -->
                    <chat-admin-form
                        :chat="currentChat.chat"
                        action-name="Delete"
                        :action-url="
                            route('chat.destroy', { chat: currentChat.chat.id })
                        "
                        method="DELETE"
                    >
                        <p class="mb-0 text-accent fw-bold">Are you sure?</p>
                    </chat-admin-form>
                    <hr class="w-100" />
                    <!-- Banned users -->
                    <template v-if="currentChat.chat.bans.length > 0">
                        <h6>
                            Banned users ({{ currentChat.chat.bans.length }}):
                        </h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                            <li
                                v-for="ban in currentChat.chat.bans"
                                class="d-flex align-items-center gap-2"
                            >
                                <!-- USER PROFILE PICTURE -->
                                <img
                                    :src="asset(ban.user.profile_picture)"
                                    :alt="
                                        ban.user.nickname + ' profile picture'
                                    "
                                    class="rounded-circle ratio-1"
                                    height="45"
                                    width="45"
                                />
                                <!-- USER NICKNAME -->
                                <a
                                    :href="
                                        route('users.show', ban.user.nickname)
                                    "
                                >
                                    {{ ban.user.nickname }}
                                </a>
                                <!-- UNBAN BUTTON -->
                                <button
                                    @click="handleUnban(ban.id)"
                                    class="btn btn-warning p-1"
                                >
                                    Unban
                                </button>
                            </li>
                        </ul>
                        <hr class="w-100" />
                    </template>
                </template>
                <!-- LEAVE CHANNEL BUTTON -->
                <form
                    v-if="currentChat.chat.type == 'channel'"
                    method="POST"
                    :action="route('chat.leave', currentChat.chat.id)"
                >
                    <input
                        type="hidden"
                        name="_token"
                        :value="axios.defaults.headers.common['X-CSRF-TOKEN']"
                    />

                    <button
                        type="submit"
                        class="border-0 bg-transparent d-flex align-items-center align-items-center gap-2 text-black px-0 mb-1"
                    >
                        <i class="bx bx-minus bx-sm"></i>
                        <span class="fw-bold">Leave</span>
                    </button>
                </form>
                <!-- TOGGLE ACTIONS BUTTON -->
                <button
                    @click="toggleActionsShown"
                    class="border-0 bg-transparent d-flex align-items-center gap-2 text-black px-0"
                >
                    <i class="bx bx-x bx-sm"></i>
                    <span class="fw-bold">Close</span>
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
                    v-show="loadingMessages"
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
                <!-- IF messages.length > 0 -->
                <template
                    v-if="messages.length > 0"
                    v-for="message in messages"
                >
                    <!-- Notification -->
                    <p
                        v-if="message.type == 'notification'"
                        class="text-white text-center"
                    >
                        {{ message.content }}
                    </p>

                    <!-- Sent message -->
                    <chat-sent-message
                        v-else-if="
                            message.type == 'user' &&
                            message.user_id == currentUser.id
                        "
                        :message="message.content"
                    ></chat-sent-message>

                    <!-- Received message -->
                    <chat-received-message
                        v-else
                        :user="message.user"
                        :message="message.content"
                    ></chat-received-message>
                </template>
                <!-- ELSE -->
                <p v-else v-if="!loadingMessages" class="text-white">
                    No messages sent yet.
                </p>
            </div>

            <!-- Send message form -->
            <form
                @submit.prevent="sendMessage"
                class="input-group shadow rounded-5"
                :class="{
                    'mt-auto': messages.length == 0,
                }"
                autocomplete="off"
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
                <a
                    :href="route('chat.channels')"
                    class="text-decoration-underline text-accent"
                >
                    Channels
                </a>
                !
            </h3>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { asset, useEmitter } from "../helper";
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

// Chat actions
const chatActions = ref(null);
const actionsShown = ref(false);

function toggleActionsShown() {
    actionsShown.value = !actionsShown.value;
    // Scroll to the top each time the chat actions menu is shown
    if (actionsShown.value) {
        chatActions.value.scrollTop = 0;
    }
}

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
            route("messages.store", { chat: currentChat.value.chat_id }),
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

        const res = await axios.get(
            route("messages.index", { chat: currentChat.value.chat_id }),
            {
                params: { page: page.value },
            }
        );

        if (res.data.message && res.data.message == "No messages yet.") {
            // TODO: finish this shit?
            return [];
        }

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

        const newMessages = await getMessages();
        if (newMessages.length == 0) {
            break;
        }

        messages.value = [...newMessages, ...messages.value];
        await nextTick();
    }

    nextTick(() => {
        scrollToBottom();
    });
}

// Admin actions
async function handleKick(userId) {
    try {
        const formData = new FormData();
        formData.append("user_id", userId);

        const res = await axios.post(
            route("chat.kick", { chat: currentChat.value.chat.id }),
            formData,
            {
                ContentType: "multipart/form-data",
            }
        );
    } catch (error) {
        console.error("User chat kick request error: " + error);
    } finally {
        // Remove the user from the users list
        currentChat.value.chat.users = currentChat.value.chat.users.filter(
            (user) => user.id != userId
        );
    }
}

async function handleBan(userId) {
    try {
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("chat_id", currentChat.value.chat.id);

        const res = await axios.post(route("userChatBans.store"), formData, {
            ContentType: "multipart/form-data",
        });
    } catch (error) {
        console.error("User chat ban request error: " + error);
    } finally {
        // Remove the user from the users list
        currentChat.value.chat.users = currentChat.value.chat.users.filter(
            (user) => user.id != userId
        );
    }
}

async function handleUnban(banId) {
    try {
        const res = await axios.delete(
            route("userChatBans.destroy", { userChatBan: banId })
        );
    } catch (error) {
        console.error("User chat ban request error: " + error);
    } finally {
        // Remove the user from the users list
        currentChat.value.chat.bans = currentChat.value.chat.bans.filter(
            (ban) => ban.id != banId
        );
    }
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
