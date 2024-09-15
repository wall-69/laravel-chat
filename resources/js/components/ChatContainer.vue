<template>
    <div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
        <div v-if="userChat" class="container-lg py-3 d-flex flex-column h-100">
            <!-- Chat info -->
            <div
                class="container-lg border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between"
            >
                <div class="d-flex align-items-center gap-2">
                    <img
                        :src="asset(userChat.picture)"
                        alt="X's profile picture"
                        class="bg-white rounded-circle"
                        height="45"
                        width="45"
                    />
                    <p class="m-0 text-white fw-bold">
                        {{ userChat.name }}
                    </p>
                </div>
                <button class="border-0 bg-transparent text-white ms-auto">
                    <i class="bx bx-dots-horizontal-rounded bx-md"></i>
                </button>
            </div>

            <!-- Chat -->
            <div
                ref="chatContainer"
                class="d-flex flex-column py-2 overflow-y-scroll px-3 mt-auto"
            >
                <!-- @foreach (userChat.chat->messages()->with('user')->get() as
                $message) @if ($message->user_id == auth()->user()->id)
                <x-chat-sent-message :message="$message->content" />
                @else
                <x-chat-received-message
                    :user="$message->user"
                    :message="$message->content"
                />
                @endif @endforeach -->
            </div>

            <!-- Input -->
            <form
                class="input-group shadow rounded-5"
                method="POST"
                :action="route('messages.store', userChat.chat_id)"
            >
                <input
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
import { onMounted, ref } from "vue";

// Props
const props = defineProps({
    userChat: Object,
});

// Echo
function joinPrivateChannel(channelName) {
    Echo.private(channelName).listen("MessageSent", (e) => {
        Livewire.dispatch("messageSent");
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

onMounted(() => {
    // Initial scroll
    scrollToBottom();

    // Initial echo join
    if (props.userChat && props.userChat.value) {
        joinPrivateChannel("chats." + props.userChat.value.chat_id);
    }
});
</script>
