<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card style="width: 720px; max-width: 90%; min-height: 550px; max-height: 95vh;">
            <q-card-section>
                <div class="text-h6">Add Patient to Queue</div>
            </q-card-section>

            <q-separator />

            <q-card-section class="relative">
                <div class="sticky-tabs">
                    <q-tabs
                        v-model="tab"
                        dense
                        align="justify"
                        class="bg-primary text-white"
                        :breakpoint="0"
                    >
                        <q-tab name="new" icon="person_add" label="New patient" />
                        <q-tab name="returning" icon="autorenew" label="Returning patient" />
                    </q-tabs>
                </div>

                <q-tab-panels v-model="tab" animated>
                    <q-tab-panel name="new">
                        <add-patient-form @cancel="open_modal = false" @success="handleAddPatientSuccess" />
                    </q-tab-panel>
                    <q-tab-panel name="returning">
                        <returning-patients @cancel="open_modal = false" @success="handleAddPatientSuccess" />
                    </q-tab-panel>
                </q-tab-panels>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>
<script setup>
import AddPatientForm from 'components/patients/AddPatientForm.vue';
import ReturningPatients from 'components/patients/ReturningPatients.vue';
import { computed, ref } from 'vue';
import { eventBus } from 'src/utils/event-bus';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
});

const tab = ref('new');

const emit = defineEmits(['update:modelValue']);

function handleAddPatientSuccess() {
    open_modal.value = false;
    eventBus.emit('refreshTable');
}

const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<style scoped>
.sticky-tabs {
    position: sticky;
    top: 0;
    z-index: 1;
}
</style>