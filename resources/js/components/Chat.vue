<template>
    <div
        @touchstart="handleTouchStart"
        @touchend="handleTouchEnd"
        class="px-0 px-md-2 container-md py-5"
    >
        <div class="row gx-0 gx-md-5">
            <!-- Desktop ChatTabs -->
            <div
                class="d-none d-md-block col-3 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll"
            >
                <chat-tab
                    v-for="userChat in chatOrder"
                    @switch-chat="handleSwitchChat"
                    :key="userChat.id"
                    :user-chat="userChat"
                >
                </chat-tab>
            </div>

            <!-- ChatContainer -->
            <div id="chat" class="col-12 col-md-9 vh-100">
                <chat-container
                    @loading-messages="handleLoadingMessages"
                    @loaded-messages="handleLoadedMessages"
                ></chat-container>
            </div>
        </div>

        <!-- Mobile ChatTabs -->
        <transition name="slide">
            <div
                v-show="mobileChatTabsShown"
                class="position-fixed w-100 vh-100 z-3 bg-primary start-0 top-0 overflow-y-scroll"
            >
                <h2 class="bg-primary text-white text-center mb-0 py-2">
                    Your chats
                </h2>
                <chat-tab
                    v-for="userChat in chatOrder"
                    @switch-chat="handleSwitchChat"
                    :key="userChat.id"
                    :user-chat="userChat"
                >
                </chat-tab>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { computed, onMounted, provide, ref, watch } from "vue";
import { asset, useEmitter } from "../helper";

/*
 *  PROPS
 */

const props = defineProps({
    currentUser: Object,
    chatOrder: Array,
});

/*
 *  EMITS
 */

const emitter = useEmitter();

/*
 *  EVENTS
 */

onMounted(() => {
    // Initial echo join
    for (let userChat in props.chatOrder) {
        joinPrivateChannel("chats." + props.chatOrder[userChat].chat_id);
    }
});

/*
 *  COMPONENT
 */

const chatOrder = ref([...props.chatOrder]);
const currentChat = ref(props.chatOrder[0]);

provide("currentUser", props.currentUser);
provide("currentChat", currentChat);

// ECHO
/**
 * Joins private echo channel for listening to event broadcasts.
 * @param channelName The name of the channel
 */
async function joinPrivateChannel(channelName) {
    // Listens in private chat channel for MessageSent event
    Echo.private(channelName)
        // MessageSent
        .listen("MessageSent", async (event) => {
            // Emit to the child components message sent event with the message
            emitter.emit("messageSent", event.message);

            // Find the index of the chat in which the message was sent
            const chatIndex = chatOrder.value.findIndex(
                (userChat) => userChat.chat_id === event.message.chat_id
            );

            // Check, if the chat was found in the chatOrder array
            if (chatIndex !== -1 && chatOrder.value.length > 1) {
                // Save the current Y scroll position, because after sorting the chat tabs, the scrollbar jumps automatically
                const scrollPosition = window.scrollY;

                // Update the last message of the chat
                const lastMessage =
                    chatOrder.value[chatIndex].chat.last_message;
                if (lastMessage !== null) {
                    lastMessage.created_at = event.message.created_at;
                } else {
                    // If there is no last message set it to the message that was just sent
                    chatOrder.value[chatIndex].chat.last_message =
                        event.message;
                }

                // Re-sort the chats
                chatOrder.value.sort((a, b) => {
                    const dateA = a.chat.last_message
                        ? new Date(a.chat.last_message.created_at)
                        : 0;
                    const dateB = b.chat.last_message
                        ? new Date(b.chat.last_message.created_at)
                        : 0;

                    return dateA === dateB ? 1 : dateB - dateA;
                });

                // Scroll to the scroll position the user actually had
                window.scrollTo(window.scrollX, scrollPosition);
            }

            if (event.message.user_id != props.currentUser.id) {
                // Play notification sound, if the message was not sent by the user
                let audio = new Audio(asset("audio/notification.mp3"));
                audio.play();
            }
        })
        // ChatAdminChanged
        .listen("ChatAdminChanged", (event) => {
            // Find the index of the chat in which the admin was changed
            const chatIndex = chatOrder.value.findIndex(
                (userChat) => userChat.chat_id === event.chatAdmin.chat_id
            );

            // Check, if the chat was found in the chatOrder array
            if (chatIndex !== -1) {
                // Change the admin of the chat to the new one
                chatOrder.value[chatIndex].chat.admin = event.chatAdmin;
            }
        })
        // ChatUpdated event
        .listen("ChatUpdated", (event) => {
            // Find the index of the chat which was updated
            const chatIndex = chatOrder.value.findIndex(
                (userChat) => userChat.chat_id === event.chatId
            );

            // Check, if the chat was found in the chatOrder array
            if (chatIndex !== -1) {
                // Update data
                for (let field in event.data) {
                    chatOrder.value[chatIndex].chat[field] = event.data[field];
                }
            }
            console.log(event.data);
        })
        // UserChatDeleted event
        .listen("UserChatDeleted", (event) => {
            // Find the index of the chat which was deleted
            const chatIndex = chatOrder.value.findIndex(
                (userChat) => userChat.chat_id === event.chatId
            );

            // Check, if the chat was found in the chatOrder array and if the UserChat id is the same as this one
            if (
                chatIndex !== -1 &&
                chatOrder.value[chatIndex].id == event.userChatId
            ) {
                // Emit the event for ChatTabs
                emitter.emit("chatDeleted", event.chatId);

                // Remove the chat from the array
                chatOrder.value.splice(chatIndex, 1);

                Echo.leave("chats." + event.chatId);

                currentChat.value =
                    chatOrder.value.length >= 1 ? chatOrder.value[0] : null;
            }
        });
}

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
    const newUserChat = props.chatOrder.find((userChat) => {
        return userChat.id == id;
    });

    // If id is invalid, throw error
    if (newUserChat) {
        currentChat.value = newUserChat;
        if (mobileChatTabsShown.value) {
            mobileChatTabsShown.value = false;
        }
    } else {
        console.error("Invalid user chat id in handleSwitchChat.");
    }
}

// Swiping logic (showing chat tabs on mobile)
const mobileChatTabsShown = ref(false);
const swipeStartX = ref(0);
const swipeStartY = ref(0);
const swipeEndX = ref(0);
const swipeEndY = ref(0);

/**
 * Function for handling the start of a touch on the screen. This saves the `swipeStartX` to the screenX position of where the touch started.
 */
function handleTouchStart(event) {
    swipeStartX.value = event.changedTouches[0].screenX;
    swipeStartY.value = event.changedTouches[0].screenY;
}

/**
 * Function for handling the end of a touch on the screen. This saves the `swipeEndX` to the screenX position of where the touch ended. Then, if the touch was from left to right, the `mobileChatTabsShown` will be set to true and if it was from right to left it will be set to false.
 */
function handleTouchEnd(event) {
    swipeEndX.value = event.changedTouches[0].screenX;
    swipeEndY.value = event.changedTouches[0].screenY;

    const differenceX = swipeEndX.value - swipeStartX.value;
    const differenceY = swipeEndY.value - swipeStartY.value;
    if (Math.abs(differenceY) > 50) {
        return;
    }
    if (differenceX > 50) {
        mobileChatTabsShown.value = true;
    } else if (differenceX < -50) {
        mobileChatTabsShown.value = false;
    }

    event.stopPropagation();
}

/*
 * WATCHERS
 */

// Mobile chat tabs watcher - adds the no-scroll class to the <body> element, when mobile chat tabs are shown and removes the class, if they are hidden.
watch(
    () => mobileChatTabsShown.value,
    (newShown, oldShown) => {
        if (newShown) {
            document.body.classList.add("no-scroll");
        } else {
            document.body.classList.remove("no-scroll");
        }
    }
);
</script>

<style>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}
.slide-enter-to,
.slide-leave-from {
    transform: translateX(0);
}
</style>
