<template>
    <q-page class="q-pa-md">
        <q-card>
            <q-card-section>
                <div class="text-h6"> <q-icon name="business" /> Medicine Brands</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <div>
                    <q-btn label="Add Brand" color="primary" icon="add" size="sm" class="q-mb-md" @click="showAddDialog = true" />
                    
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-form @submit="getList(true)" class="q-pb-md">
                                <q-input label="Search Brand" outlined dense v-model="list_params.search">
                                    <template v-slot:append>
                                        <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                                    </template>
                                </q-input>
                            </q-form>
                        </div>
                    </div>

                    <q-table 
                        :columns="columns"
                        :rows="items_list"
                        row-key="id"
                        :rows-per-page-options="[list_params.per_page]"
                        :hide-bottom="items_list.length > 0"
                        :loading="fetching"
                    >
                        <template v-slot:body-cell-actions="props">
                            <q-td :props="props">
                                <div class="q-gutter-sm">
                                    <q-btn round color="primary" icon="edit" size="sm" @click="editItem(props.row)" />
                                    <q-btn round color="negative" icon="delete" size="sm" @click="confirmDelete(props.row)" />
                                </div>
                            </q-td>
                        </template>
                    </q-table>

                    <div v-if="items_list.length > 0 && list_request && list_request.data && list_request.data.last_page > 0"
                        class="flex justify-between q-my-md">
                        <q-pagination 
                            v-model="list_params.page"
                            :max="list_request.data.last_page"
                            direction-links
                            :max-pages="6"
                            flat
                            color="grey" 
                            active-color="indigo-14"
                            @update:model-value="getList()"
                        />
                        <span>
                            Showing {{ list_request.data.from }} to {{ list_request.data.to }} of {{ list_request.data.total }}
                        </span>
                    </div>

                    <!-- Add/Edit Dialog -->
                    <q-dialog v-model="showAddDialog">
                        <q-card style="min-width: 400px">
                            <q-card-section class="bg-primary text-white">
                                <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} Medicine Brand</div>
                            </q-card-section>

                            <q-card-section>
                                <q-form @submit="saveItem">
                                    <q-input
                                        v-model="formData.name"
                                        label="Brand Name"
                                        outlined
                                        dense
                                        :disable="saving"
                                        :rules="[val => !!val || 'Name is required']"
                                    />
                                </q-form>
                            </q-card-section>
                            <q-separator />
                            <q-card-section>
                                <div class="q-gutter-sm">
                                    <q-btn :label="editMode ? 'Update' : 'Save'" type="submit" color="primary" :loading="saving" icon="check_circle" @click="saveItem" />
                                    <q-btn label="Cancel" color="grey-8" outline v-close-popup class="q-mr-sm" icon="cancel" />
                                </div>
                            </q-card-section>
                        </q-card>
                    </q-dialog>
                </div>
            </q-card-section>
        </q-card>
        
        <confirmation-modal
            v-model="open_confirmation_modal"
            title="Confirm Removal"
            :message="`Are you sure you want to remove <strong>${selected_item?.name}</strong> from the list?`"
            confirm_label="Yes, Remove"
            cancel_label="Cancel"
            @confirm="handleConfirmRemoveItem"
        />
    </q-page>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useMedicineStore } from 'src/stores/medicine';
import { onMounted, ref, getCurrentInstance, watch } from 'vue';
import ConfirmationModal from 'src/components/modals/ConfirmationModal.vue';

const medicine_store = useMedicineStore();
const { listMedicineBrands, createMedicineBrand, updateMedicineBrand, deleteMedicineBrand } = medicine_store;
const { medicine_brand_list_request } = storeToRefs(medicine_store);

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const columns = [
    { name: 'name', label: 'Brand Name', field: 'name', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'right' },
];

const list_params = ref({
    per_page: 10,
    page: 1,
    search: '',
    paginate: 'true',
});

const items_list = ref([]);
const fetching = ref(false);
const showAddDialog = ref(false);
const editMode = ref(false);
const saving = ref(false);
const list_request = ref(null);

const formData = ref({
    id: null,
    name: '',
});

const selected_item = ref(null);
const open_confirmation_modal = ref(false);

async function getList(is_search = false) {
    try {
        if (is_search) {
            list_params.value.page = 1;
        }

        fetching.value = true;
        await listMedicineBrands(list_params.value);

        const response = medicine_brand_list_request.value;
        list_request.value = response;

        if (response.success) {
            items_list.value = response.data.data || response.data;
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
        fetching.value = false;
    }
}

function editItem(item) {
    editMode.value = true;
    formData.value.id = item.id;
    formData.value.name = item.name;
    showAddDialog.value = true;
}

function confirmDelete(item) {
    open_confirmation_modal.value = true;
    selected_item.value = item;
}

const handleConfirmRemoveItem = async () => {
    if (!selected_item.value) return;

    await deleteItem(selected_item.value.id);
    open_confirmation_modal.value = false;
    selected_item.value = null;
}

async function deleteItem(id) {
    try {
        await deleteMedicineBrand(id);
        const response = medicine_store.medicine_brand_delete_request;

        if (response.success) {
            Notify.create({
                message: 'Medicine brand deleted successfully',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green',
            });
            getList();
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
    }
}

async function saveItem() {
    try {
        saving.value = true;
        
        if (editMode.value) {
            await updateMedicineBrand(formData.value.id, { name: formData.value.name });
            const response = medicine_store.medicine_brand_update_request;
            
            if (response.success) {
                Notify.create({
                    message: 'Medicine brand updated successfully',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'green',
                });
                showAddDialog.value = false;
                resetForm();
                getList();
            } else {
                Notify.create({
                    message: response.message,
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'red-14',
                });
            }
        } else {
            await createMedicineBrand({ name: formData.value.name });
            const response = medicine_store.medicine_brand_create_request;
            
            if (response.success) {
                Notify.create({
                    message: 'Medicine brand saved successfully',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'green',
                });
                showAddDialog.value = false;
                resetForm();
                getList();
            } else {
                Notify.create({
                    message: response.message,
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'red-14',
                });
            }
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
        saving.value = false;
    }
}

function resetForm() {
    formData.value = {
        id: null,
        name: '',
    };
    editMode.value = false;
}

watch(showAddDialog, (newVal) => {
    if (!newVal) {
        resetForm();
    }
});

onMounted(() => {
    getList(true);
});
</script>
