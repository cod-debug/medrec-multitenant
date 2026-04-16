<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card :style="{ width: props.size, maxWidth: '90%' }">
            <q-card-section class="text-center">
                <div class="text-h6 q-py-md"><q-icon :name="icon" /> <span v-html="title"></span></div>
                <div><span v-html="message"></span></div>
            </q-card-section>
            <q-separator />
            <q-card-section class="text-center">
                <q-btn :color="confirm_btn_color" :label="confirm_label" @click="emit('confirm')" icon="check_circle" />
                <q-btn outline :label="cancel_label" color="grey-8" icon="cancel" @click="open_modal = false" class="q-ml-sm" />
            </q-card-section>
        </q-card>
    </q-dialog>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    confirm_btn_color: {
        type: String,
        default: 'red-14'
    },
    size: {
        type: String,
        default: '450px'
    },
    icon: {
        type: String,
        default: 'help_outline'
    },
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        required: true
    },
    confirm_label: {
        type: String,
        default: 'Confirm'
    },
    cancel_label: {
        type: String,
        default: 'Cancel'
    }
});

const emit = defineEmits(['update:modelValue', 'confirm']);

const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>