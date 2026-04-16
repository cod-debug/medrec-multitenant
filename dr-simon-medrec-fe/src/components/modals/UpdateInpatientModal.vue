<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card :style="{ width: props.size, maxWidth: '90%' }">
            <q-card-section class="bg-primary text-white">
                <div class="text-h6">Update Inpatient Information</div>
            </q-card-section>
            <q-card-section>
                <q-form @submit.prevent="handleUpdate">
                    <q-input v-model="form.full_name" label="Full Name" outlined :rules="[rules.required]" />
                    <q-input v-model="form.age" label="Age" type="number" outlined :rules="[rules.required]" />
                    <q-select v-model="form.gender" label="Sex" outlined :rules="[rules.selectRequired]" :options="['Male', 'Female']" />
                </q-form>
            </q-card-section>
            <q-card-actions>
                <q-btn label="Update" color="primary" @click="handleUpdate" icon="check_circle" />
                <q-btn label="Close" color="grey-9" outline @click="open_modal = false" icon="cancel" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
<script setup>
import { computed, ref, getCurrentInstance, watch } from 'vue';
import { useInpatientStore } from 'src/stores/inpatient';
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';
import { eventBus } from 'src/utils/event-bus';
const spinner = useSpinner();

const inpatient_store = useInpatientStore();
const { updateInpatient } = inpatient_store;
const { update_inpatient_request } = storeToRefs(inpatient_store);

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: '620px'
    },
    selected_inpatient: {
        type: Object,
        required: true
    }
});

const form = ref({});

// methods
async function handleUpdate() {
    try {
        spinner.show('Updating inpatient information...');
        await updateInpatient(form.value);
        const response = update_inpatient_request.value;
        if (response?.success) {
            open_modal.value = false;
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            
            emit('update:modelValue', false);
            eventBus.emit('updateInpatientInfo', response.data.inpatient);
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        helpers.showDebugMessage(error);
        Notify.create({
            message: 'An error occurred while updating inpatient information.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}


const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

watch(() => props.selected_inpatient, (new_inpatient) => {
    if (new_inpatient) {
        form.value = {
            id: new_inpatient.id,
            full_name: new_inpatient.full_name,
            gender: new_inpatient.gender,
            age: new_inpatient.age,
        };
    }
});
</script>
