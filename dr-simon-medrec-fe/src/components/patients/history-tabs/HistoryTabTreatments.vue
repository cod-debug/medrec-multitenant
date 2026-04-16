<template>
    <ul>
        <li v-for="(treatment, index) in treatments" :key="index">
            {{ treatment?.treatment || 'N/A' }}
            <ul v-if="treatment.remarks">
                <li>{{ treatment.remarks }}</li>
            </ul>
        </li>
    </ul>
</template>

<script setup>
const { treatments } = defineProps({
    treatments: {
        type: Array,
        required: true
    },
});
</script>