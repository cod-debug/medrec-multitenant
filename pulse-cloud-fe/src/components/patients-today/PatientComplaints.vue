<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white">
            <div class="text-subtitle2">Patient's Complaints</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="row q-mb-sm rounded" v-for="(complaint, key) in localComplaints" :key="`complaint-${key + 1}`">
                <div :class="`col-12 ${localComplaints.length > 1 && !is_readonly ? 'col-md-11' : 'col-md-12'} q-pa-sm`">
                    <q-input type="textarea" :readonly="is_readonly" label="Complaint" outlined v-model="complaint.name"
                        :rules="[]" />
                </div>
                <div class="col-12 col-md-1 q-pa-sm" v-if="!is_readonly">
                    <div style="width: 100%; height: 100%;" class="flex items-center justify-center">
                        <q-btn color="red-14" icon="delete" size="sm" round
                            @click="removeInformation('complaint', key, complaint.id)" v-if="localComplaints.length > 1">
                            <q-tooltip class="bg-grey-14 text-white">Remove Complaint</q-tooltip>
                        </q-btn>
                    </div>
                </div>
            </div>
            <div>
                <q-btn color="primary" label="Add Complaint" icon="add" size="sm" outline v-if="!is_readonly"
                    @click="addComplaint" />
            </div>
        </q-card-section>
    </q-card>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    complaints: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['update:complaints', 'remove']);

const localComplaints = ref([...props.complaints]);

watch(() => props.complaints, (newVal) => {
    localComplaints.value = [...newVal];
}, { deep: true });

const addComplaint = () => {
    localComplaints.value.push({ id: null, name: '', remarks: '' });
    emit('update:complaints', localComplaints.value);
};

const removeInformation = (type, index, id) => {
    localComplaints.value.splice(index, 1);
    emit('update:complaints', localComplaints.value);
    if (id) {
        emit('remove', type, index, id);
    }
};

</script>