<template>
    <div>
        <TodaysPatientTable @refreshTable="getList(true)" />
    </div>
</template>
<script setup>
import TodaysPatientTable from './TodaysPatientTable.vue';
</script>