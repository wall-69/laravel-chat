<template>
    <div class="mb-1">
        <button
            v-if="!formShown"
            @click="toggleFormShown"
            class="btn btn-primary p-1"
        >
            {{ actionName }}
        </button>
        <form
            v-else
            method="POST"
            :action="route('chat.update', chat.id)"
            class="d-flex align-items-center flex-wrap gap-1"
        >
            <input
                type="hidden"
                name="_token"
                :value="axios.defaults.headers.common['X-CSRF-TOKEN']"
            />
            <input type="hidden" name="_method" value="PATCH" />

            <slot></slot>
            <button type="submit" class="btn btn-primary p-1">Submit</button>
            <button @click="toggleFormShown" class="btn btn-secondary p-1">
                Cancel
            </button>
        </form>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";

/*
 *  PROPS
 */
const props = defineProps({
    chat: Object,
    actionName: String,
});

/*
 *  COMPONENT
 */

const formShown = ref(false);

function toggleFormShown() {
    formShown.value = !formShown.value;
}
</script>
