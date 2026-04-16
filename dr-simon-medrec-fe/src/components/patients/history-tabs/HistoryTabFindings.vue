<template>
    <div>
        <ul>
            <li v-for="(finding, index) in findings" :key="index">
                {{ finding?.finding || 'N/A' }}
                <ul v-if="finding.finding_remarks">
                    <li>{{ finding.finding_remarks }}</li>
                </ul>
            </li>
        </ul>
        <q-card style="height: 100%;" v-if="findings_images && findings_images.length > 0" class="q-mt-md">
            <q-card-section>
                <div class="q-pa-md">
                    <q-carousel
                    animated
                    v-model="findings_slides"
                    :arrows="findings_images.length > 1"
                    :navigation="findings_images.length > 1"
                    navigation-size="sm"
                    infinite
                    control-color="primary"
                    >
                    <q-carousel-slide :name="index + 1"
                        v-for="(findings_image, index) in findings_images"
                        :key="index"
                    >
                        <img :src="findings_image.image_url" 
                            style="width: 100%; height: 100%; object-fit: contain;" 
                            @click="image_zoom_modal.openZoom(findings_image.image_url)"
                            alt="Finding image" />
                    </q-carousel-slide>
                    </q-carousel>
                </div>
            </q-card-section>
        </q-card>
        <image-zoom-modal ref="image_zoom_modal" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ImageZoomModal from 'src/components/modals/ImageZoomModal.vue';

const image_zoom_modal = ref(null);
const findings_slides = ref(1);
const { findings, findings_images } = defineProps({
    findings: {
        type: Array,
        required: true
    },
    findings_images: {
        type: Array,
        required: false,
        default: () => []
    }
});
</script>