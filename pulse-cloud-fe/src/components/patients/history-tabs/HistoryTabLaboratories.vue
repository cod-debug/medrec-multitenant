<template>
    <div>
        <ul>
            <li v-for="(laboratory, index) in laboratories" :key="index">
                {{ laboratory?.laboratory_test || 'N/A' }}
                <ul v-if="laboratory.finding_remarks">
                    <li>{{ laboratory.finding_remarks }}</li>
                </ul>
            </li>
        </ul>
        <q-card style="height: 100%;" v-if="laboratories_images && laboratories_images.length > 0" class="q-mt-md">
            <q-card-section>
                <div class="q-pa-md">
                    <q-carousel
                    animated
                    v-model="laboratories_slides"
                    :arrows="laboratories_images.length > 1"
                    :navigation="laboratories_images.length > 1"
                    navigation-size="sm"
                    infinite
                    >
                        <q-carousel-slide :name="index + 1"
                            v-for="(laboratories_image, index) in laboratories_images"
                            :key="index"
                        >
                            <img :src="laboratories_image.image_url" 
                                style="width: 100%; height: 100%; object-fit: contain;" 
                                @click="image_zoom_modal.openZoom(laboratories_image.image_url)"
                                alt="Laboratory image" />
                        </q-carousel-slide>
                    </q-carousel>
                </div>
            </q-card-section>
        </q-card>
        <image-zoom-modal ref="image_zoom_modal" />
    </div>
</template>

<script setup>
import ImageZoomModal from 'src/components/modals/ImageZoomModal.vue';
import { ref } from 'vue';

const image_zoom_modal = ref(null);
const laboratories_slides = ref(1);
const { laboratories, laboratories_images } = defineProps({
    laboratories: {
        type: Array,
        required: true
    },
    laboratories_images: {
        type: Array,
        required: false,
        default: () => []
    }
});
</script>