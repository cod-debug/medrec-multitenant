<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white">
            <div class="text-subtitle2">Laboratories</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="row q-mb-sm rounded" v-for="(laboratory, key) in localLaboratories" :key="`laboratory-${key + 1}`">
                <div :class="`col-12 ${localLaboratories.length > 1 && !is_readonly ? 'col-md-11' : 'col-md-12'} q-pa-sm`">
                    <q-input type="textarea" :readonly="is_readonly" label="Laboratory" outlined v-model="laboratory.name"
                        :rules="[]" />
                </div>
                <div class="col-12 col-md-1 q-pa-sm" v-if="!is_readonly">
                    <div style="width: 100%; height: 100%;" class="flex items-center justify-center">
                        <q-btn color="red-14" icon="delete" size="sm" round
                            @click="removeInformation('laboratory', key, laboratory.id)" v-if="localLaboratories.length > 1">
                            <q-tooltip class="bg-grey-14 text-white">Remove Laboratory</q-tooltip>
                        </q-btn>
                    </div>
                </div>
            </div>
            <div>
                <q-btn color="primary" label="Add Laboratory" icon="add" size="sm" outline v-if="!is_readonly"
                    @click="addLaboratory" />
            </div>
        </q-card-section>
        <open-camera @capture="handleCaptureLaboratoryImage" />
        <div class="q-px-md">
            <div class="row q-mt-md" v-if="laboratory_images.length > 0">
                <div class="col-12">
                    <div class="text-subtitle2 text-grey-9">Laboratory Images</div>
                </div>
                <div class="col-12 col-md-6 q-pa-sm" v-for="(image, key) in laboratory_images" :key="`laboratory-image-${key+1}`">
                    <div style="position: relative; width: 100%; height: auto;" class="flex items-center justify-center">
                        <q-img :src="image.image_url" :ratio="1" class="rounded shadow-2" />
                        <q-btn icon="delete" color="red-14" size="sm" round @click="removeLaboratoryImage(image, key)" class="absolute" style="top: -10px; right: -10px;" v-if="!is_readonly" />
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
    laboratory_images: {
        type: Array,
        default: () => []
    },
    laboratories: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['update:laboratories', 'update:laboratory_images', 'remove', 'capture', 'removeImage']);

const localLaboratories = ref([...props.laboratories]);

watch(() => props.laboratories, (newVal) => {
    localLaboratories.value = [...newVal];
}, { deep: true });

watch(() => props.laboratory_images, (newVal) => {
    emit('update:laboratory_images', newVal);
}, { deep: true });

const handleCaptureLaboratoryImage = (imageData) => {
    emit('capture', imageData);
};

const removeLaboratoryImage = (image, index) => {
    emit('removeImage',  image, index);
};

const addLaboratory = () => {
    localLaboratories.value.push({ id: null, name: '', remarks: '' });
    emit('update:laboratories', localLaboratories.value);
};

const removeInformation = (type, index, id) => {
    localLaboratories.value.splice(index, 1);
    emit('update:laboratories', localLaboratories.value);
    if (id) {
        emit('remove', type, index, id);
    }
};

</script>