<template>
    <div
        class="me-auto bg-primary rounded-4 my-1 received-message-shadow d-flex align-items-center gap-2 px-2 py-1"
    >
        <img
            :src="
                user ? asset(user.profile_picture) : '/img/chat/male_avatar.svg'
            "
            alt=""
            width="35"
            height="35"
            class="bg-white rounded-circle align-self-start"
        />
        <div class="d-flex flex-column">
            <span class="lh-1 fs-6">{{ user.nickname }} </span>
            <p class="m-0 text-white text-break">
                {{ message }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { asset } from "../helper";

const props = defineProps({
    user: Object,
    message: String,
});
</script>
