<template>
    <div class="q-pa-md public-layout">
        <q-layout view="lHh Lpr lff">
            <q-page-container>
                <router-view />
            </q-page-container>
        </q-layout>
    </div>
</template>
<script setup>

</script>
<style scoped>
.public-layout {
    background-image: url('/images/login-background.jpeg');
    background-size: cover;
    background-attachment: fixed;
    max-height: 100vh;
    overflow: auto;
}
</style>