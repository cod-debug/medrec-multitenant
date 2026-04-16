<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card style="width: 600px; max-width: 90vw;">
            <q-card-section>
                <div class="text-h6"><q-icon name="local_hotel" /> Add Inpatient Admission / Referral</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <inpatient-add-form @cancel="open_modal = false" />
            </q-card-section>
        </q-card>
    </q-dialog>
</template>

<script setup>
import InpatientAddForm from '../inpatient/InpatientAddForm.vue';
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
});
const emit = defineEmits(['update:modelValue']);

const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>