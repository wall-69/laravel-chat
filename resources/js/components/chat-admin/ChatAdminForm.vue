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
            ref="form"
            @submit="handleFormSubmit"
            method="POST"
            :action="route('chat.update', { chat: chat.id })"
            class="d-flex align-items-center flex-wrap gap-1"
        >
            <input
                v-if="method != 'POST'"
                type="hidden"
                name="_method"
                :value="method"
            />

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
import { reactive, ref } from "vue";

/*
 *  PROPS
 */
const props = defineProps({
    chat: Object,
    actionName: String,
    actionUrl: {
        type: String,
        default: (props) => route("chat.update", { chat: props.chat.id }),
    },
    method: String,
});

/*
 *  COMPONENT
 */

const form = ref(null);
const formShown = ref(false);

function toggleFormShown() {
    formShown.value = !formShown.value;
}

async function handleFormSubmit() {
    formShown.value = false;

    try {
        const formData = new FormData();
        // Get all elements inside the form, if the element has a name attribute (thus is a input), we add it to the FormData with its name & value
        for (let i = 0; i < form.value.length; i++) {
            let el = form.value[i];
            if (el.name) {
                formData.append(el.name, el.value);
            }
        }

        const res = await axios.post(props.actionUrl, formData, {
            ContentType: "multipart/form-data",
        });
    } catch (error) {
        console.error("Chat update request error: " + error);
    }
}
</script>
