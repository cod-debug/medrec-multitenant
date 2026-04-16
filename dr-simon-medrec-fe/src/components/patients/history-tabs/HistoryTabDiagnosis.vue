<template>
    <ul>
        <li v-for="(diag, index) in diagnoses" :key="index">
            {{ diag?.diagnosis || 'N/A' }}
            <ul v-if="diag.diagnosis_remarks || diag.remarks">
                <li>{{ diag.diagnosis_remarks || diag.remarks }}</li>
            </ul>
        </li>
    </ul>
</template>

<script setup>
const { diagnoses } = defineProps({
    diagnoses: {
        type: Array,
        required: true
    }
});
</script>