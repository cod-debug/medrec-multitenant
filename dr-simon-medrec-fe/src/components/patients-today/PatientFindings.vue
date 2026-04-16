<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white">
            <div class="text-subtitle2">PE Findings</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="row q-mb-sm rounded" v-for="(finding, key) in localFindings" :key="`finding-${key + 1}`">
                <div :class="`col-12 ${localFindings.length > 1 && !is_readonly ? 'col-md-11' : 'col-md-12'} q-pa-sm`">
                    <q-input type="textarea" :readonly="is_readonly" label="PE Findings" outlined v-model="finding.name"
                        :rules="[]" />
                </div>
                <div class="col-12 col-md-1 q-pa-sm" v-if="!is_readonly">
                    <div style="width: 100%; height: 100%;" class="flex items-center justify-center">
                        <q-btn color="red-14" icon="delete" size="sm" round
                            @click="removeInformation('finding', key, finding.id)" v-if="localFindings.length > 1">
                            <q-tooltip class="bg-grey-14 text-white">Remove Findings</q-tooltip>
                        </q-btn>
                    </div>
                </div>
            </div>
            <div>
                <q-btn color="primary" label="Add Finding" icon="add" size="sm" outline v-if="!is_readonly"
                    @click="addFinding" />
            </div>
        </q-card-section>
        <open-camera @capture="handleCaptureFindingsImage" />
        <div class="q-px-md">
            <div class="row q-mt-md" v-if="findings_images.length > 0">
                <div class="col-12">
                    <div class="text-subtitle2 text-grey-9">Findings Images</div>
                </div>
                <div class="col-12 col-md-6 q-pa-sm" v-for="(image, key) in findings_images" :key="`findings-image-${key+1}`">
                    <div style="position: relative; width: 100%; height: auto;" class="flex items-center justify-center">
                        <q-img :src="image.image_url" :ratio="1" class="rounded shadow-2" />
                        <q-btn icon="delete" color="red-14" size="sm" round @click="removeFindingsImage(image, key)" class="absolute" style="top: -10px; right: -10px;" />
                    </div>
                </div>
            </div>
        </div>
    </q-card>
</template>
<script setup>
import OpenCamera from 'components/reusables/OpenCamera.vue';
import { ref, watch } from 'vue';
const props = defineProps({
    findings_images: {
        type: Array,
        default: () => []
    },
    findings: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['update:findings', 'update:findings_images', 'remove', 'capture', 'removeImage']);

const localFindings = ref([...props.findings]);

watch(() => props.findings, (newVal) => {
    localFindings.value = [...newVal];
}, { deep: true });

watch(() => props.findings_images, (newVal) => {
    emit('update:findings_images', newVal);
}, { deep: true });

const handleCaptureFindingsImage = (imageData) => {
    emit('capture', imageData);
};

const removeFindingsImage = (image, index) => {
    emit('removeImage',  image, index);
};

const addFinding = () => {
    localFindings.value.push({ id: null, name: '', remarks: '' });
    emit('update:findings', localFindings.value);
};

const removeInformation = (type, index, id) => {
    localFindings.value.splice(index, 1);
    emit('update:findings', localFindings.value);
    if (id) {
        emit('remove', type, index, id);
    }
};

</script>