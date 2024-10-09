<template>
    <div
        @touchstart="handleTouchStart"
        @touchend="handleTouchEnd"
        class="px-2 px-md-0 container-md py-5"
    >
        <div class="row gx-0 gx-md-5">
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

            <div id="chat" class="col-12 col-md-9 vh-100">
                <chat-container
                    @loading-messages="handleLoadingMessages"
                    @loaded-messages="handleLoadedMessages"
                ></chat-container>
            </div>
        </div>

        <div
            v-if="mobileChatTabsShown"
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
    </div>
</template>

<script setup>
import { onMounted, provide, ref, watch } from "vue";
import { asset, useEmitter } from "../helper";

/*
 *  PROPS
 */

const props = defineProps({
    currentUser: Object,
    chatOrder: Array,
    userChats: Array,
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
    Echo.private(channelName).listen("MessageSent", async (e) => {
        // Emit to the child components message sent event with the message
        emitter.emit("messageSent", e.message);

        // Find the index of the chat in which the message was sent
        const chatIndex = chatOrder.value.findIndex(
            (userChat) => userChat.chat_id === e.message.chat_id
        );

        // Check, if the chat was found in the chatOrder array
        if (chatIndex !== -1 && chatOrder.value.length > 1) {
            // Save the current Y scroll position, because after sorting the chat tabs, the scrollbar jumps automatically
            const scrollPosition = window.scrollY;

            // Update the last message of the chat
            const lastMessage = chatOrder.value[chatIndex].chat.last_message;
            if (lastMessage !== null) {
                lastMessage.created_at = e.message.created_at;
            } else {
                // If there is no last message set it to the message that was just sent
                chatOrder.value[chatIndex].chat.last_message = e.message;
            }

            // Re-sort the chats
            chatOrder.value.sort((a, b) => {
                return (
                    new Date(b.chat.last_message.created_at) -
                    new Date(a.chat.last_message.created_at)
                );
            });

            // Scroll to the scroll position the user actually had
            window.scrollTo(window.scrollX, scrollPosition);
        }

        if (e.message.user_id != props.currentUser.id) {
            // Play notification sound, if the message was not sent by the user
            let audio = new Audio(asset("audio/notification.mp3"));
            audio.play();
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
    const newUserChat = props.userChats.find((userChat) => {
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

// Swiping logic
const mobileChatTabsShown = ref(false);
const swipeStartX = ref(0);
const swipeEndX = ref(0);

function handleTouchStart(e) {
    swipeStartX.value = e.changedTouches[0].screenX;
}

function handleTouchEnd(e) {
    swipeEndX.value = e.changedTouches[0].screenX;

    const difference = swipeEndX.value - swipeStartX.value;
    if (difference > 50) {
        mobileChatTabsShown.value = true;
    } else if (difference < -50) {
        mobileChatTabsShown.value = false;
    }
}

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
