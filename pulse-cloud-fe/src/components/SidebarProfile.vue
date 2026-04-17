<template>
    <div class="q-pa-md bg-grey-3">
        <img alt="boy avatar" :src="profile_picture" style="width: 90%;" class="q-mb-md">
        <div v-if="level_of_authorization == 1" >
            <div class="text-weight-bold">{{ full_name }}</div>
            <div>{{ user_type_parsed[level_of_authorization - 1] }}</div>
        </div>
        <div v-if="level_of_authorization == 2" class="q-mt-sm">
            <div class="text-weight-bold">Dr. {{ auth_store.doctor?.name }}</div>
            <div>
                <span>Doctor </span>
                <q-btn icon="sync_alt" :to="{ name: 'select-doctor' }" flat size="sm" class="text-secondary" v-if="!auth_store.doctor?.only_one_available">
                    <q-tooltip>
                        Switch Doctor
                    </q-tooltip>
                </q-btn>
            </div>
        </div>
    </div>
</template>
<script setup>
import { storeToRefs } from 'pinia';
import { useAuthStore } from 'src/stores/auth';
import { computed } from 'vue';

import profile_picture from 'assets/images/logo-horizontal.png';


const auth_store = useAuthStore();
const { login_request } = storeToRefs(auth_store);

const full_name = computed(() => {
  return login_request.value?.name || auth_store.getFullName;
});

const level_of_authorization = computed(() => auth_store.getLevelOfAuthorization);
const user_type_parsed = ['Doctor', 'Assistant'];

</script>

<style lang="scss" scoped>
.profile-settings {
    top: 5px;
    right: 5px;
}
</style>