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
    background: url('/images/login-background.png'), linear-gradient(to top, rgba(0,0,0, .7), rgba(0,0,0, .5));
    background-blend-mode: multiply;
    background-size: cover;
    background-attachment: fixed;
    background-position: center;
    min-height: 100vh;
    overflow: auto;
}
</style>