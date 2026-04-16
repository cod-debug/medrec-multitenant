<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white">
            <div class="text-subtitle2">Diagnosis</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="q-pa-sm bg-grey-2 q-mb-sm rounded" style="position: relative; border-radius: 5px;" v-for="(diagnosis, key) in localDiagnoses" :key="`diagnosis-${key + 1}`">
                <div class="absolute flex items-center justify-center" style="top: -.5rem; right: -.5rem;" v-if="!is_readonly">
                    <q-btn color="red-14" icon="delete" size="sm" round
                        @click="removeInformation('diagnosis', key, diagnosis.id)" v-if="localDiagnoses.length > 1">
                        <q-tooltip class="bg-grey-14 text-white">Remove Diagnosis</q-tooltip>
                    </q-btn>
                </div>
                <div class="row" >
                    <div :class="`col-12 q-pa-sm`">
                        <q-input type="textarea" :readonly="is_readonly" label="Diagnosis" outlined v-model="diagnosis.name"
                            :rules="[]" />
                    </div>
                    <div :class="`col-12 q-pa-sm`">
                        <q-input type="textarea" :readonly="is_readonly" label="Remarks" outlined v-model="diagnosis.remarks"
                            :rules="[]" />
                    </div>
                </div>
            </div>
            <div>
                <q-btn color="primary" label="Add Diagnosis" icon="add" size="sm" outline v-if="!is_readonly"
                    @click="addDiagnosis" />
            </div>
        </q-card-section>
    </q-card>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    diagnoses: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['update:diagnoses', 'remove']);

const localDiagnoses = ref([...props.diagnoses]);

watch(() => props.diagnoses, (newVal) => {
    localDiagnoses.value = [...newVal];
}, { deep: true });

const addDiagnosis = () => {
    localDiagnoses.value.push({ id: null, name: '', remarks: '' });
    emit('update:diagnoses', localDiagnoses.value);
};

const removeInformation = (type, index, id) => {
    localDiagnoses.value.splice(index, 1);
    emit('update:diagnoses', localDiagnoses.value);
    if (id) {
        emit('remove', type, index, id);
    }
};

</script>