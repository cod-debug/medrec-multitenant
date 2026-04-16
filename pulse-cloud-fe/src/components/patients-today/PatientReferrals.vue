<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white flex items-center justify-between">
            <div class="text-subtitle2">Referral</div>
            <div v-if="localReferrals.length > 0 && localReferrals[0].description"><q-btn color="primary" label="Print" icon="print" @click="$emit('print')" /></div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="row q-mb-sm rounded" v-for="(referral, key) in localReferrals" :key="`referral-${key + 1}`">
                <div :class="`col-12 ${localReferrals.length > 1 && !is_readonly ? 'col-md-11' : 'col-md-12'} q-pa-sm`">
                    <q-input type="textarea" :readonly="is_readonly" label="Referral Description" outlined v-model="referral.description"
                        :rules="[]" />
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    referrals: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    }
});

defineEmits(['print']);

const localReferrals = ref([...props.referrals]);

watch(() => props.referrals, (newVal) => {
    localReferrals.value = [...newVal];
}, { deep: true });

</script>