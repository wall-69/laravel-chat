<template>
    <div class="ms-auto bg-secondary rounded-4 my-1 sent-message-shadow">
        <p class="m-0 px-3 py-2 text-white text-break">
            {{ message }}
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    message: String,
});
</script>
