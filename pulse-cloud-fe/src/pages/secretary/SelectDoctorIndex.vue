<template>
    <q-page class="q-pa-md">
        <q-card>
            <q-card-section>
                <div class="text-h6">
                    <q-icon name="manage_accounts" />
                    <span class="q-mx-md">Select Doctor</span>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <router-view />
            </q-card-section>
        </q-card>
    </q-page>
</template>