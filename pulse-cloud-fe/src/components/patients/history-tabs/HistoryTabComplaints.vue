<template>
    <ul>
        <li v-for="(complaint, index) in complaints" :key="index">
            {{ complaint?.complaint || 'N/A' }}
            <ul v-if="complaint.complaint_remarks">
                <li>{{ complaint.complaint_remarks }}</li>
            </ul>
        </li>
    </ul>
</template>

<script setup>
const { complaints } = defineProps({
    complaints: {
        type: Array,
        required: true
    }
});
</script>