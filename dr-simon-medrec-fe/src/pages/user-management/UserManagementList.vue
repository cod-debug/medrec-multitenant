<template>
    <div>
        <q-btn label="Add User" :to="{ name: 'user-management-add' }" color="primary" icon="person_add" size="sm" class="q-mb-md" />
        <div class="row">
            <div class="col-12 col-md-6">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search User" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
        </div>

        <q-table :columns="columns"
            :rows="user_list"
            row-key="name"
            :rows-per-page-options="[list_params.per_page]"
            :hide-bottom="user_list.length > 0"
            :loading="fetching_user"
        >
            <template v-slot:body-cell-role="props">
                <q-td :props="props">
                    <q-badge :color="props.row.level_of_authorization == 1 ? 'primary' : 'secondary'" class="text-lowercase">{{ helpers.formatUserRole(props.row.level_of_authorization) }}</q-badge>
                </q-td>
            </template>
            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn flat color="primary" label="Edit" @click="helpers.redirectTo(`/user-management/${props.row.id}`)" />
                </q-td>
            </template>
        </q-table>
        <div v-if="user_list.length > 0 && user_list_request && user_list_request.data && user_list_request.data.last_page > 0"
            class="flex justify-between q-my-md">
            <q-pagination v-model="list_params.page"
                :max="user_list_request.data.last_page"
                direction-links
                :max-pages="6"
                flat
                color="grey" 
                active-color="indigo-14"
                @update:model-value="getList()"
            />
            <span>
                Showing {{ user_list_request.data.from }} to {{ user_list_request.data.to }} of {{ user_list_request.data.total }}
            </span>
        </div>
    </div>
</template>
<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useUsersStore } from 'src/stores/users';
import { onMounted, ref, getCurrentInstance } from 'vue';

// user store
const user_store = useUsersStore();
const { getUserListRequest } = user_store;
const { user_list_request } = storeToRefs(user_store);

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const columns = [
    { name: 'name', label: 'Full Name', field: 'name', align: 'left' },
    { name: 'email', label: 'Email Address', field: 'email', align: 'left' },
    { name: 'username', label: 'Username', field: 'username', align: 'left' },
    { name: 'role', label: 'Role', field: 'role', align: 'left' },
    // { name: 'actions', label: 'Actions', field: 'actions', align: 'left' },
];
const list_params = ref({
    per_page: 10,
    page: 1,
    keyword: '',
});
const user_list = ref([]);
const fetching_user = ref(false);

async function getList(is_search = false) {
    try {
        if(is_search ){
            list_params.value.page = 1;
        }

        fetching_user.value = true;
        await getUserListRequest(list_params.value);

        const response = user_list_request.value;

        if (response.success) {
            user_list.value = response.data.data;
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14',
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: `Something went wrong. Please contact administrator`,
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14',
        });
    } finally {
        fetching_user.value = false;
    }
}

onMounted(() => {
    getList(true);
});
</script>
