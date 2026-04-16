<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white">
            <div class="text-subtitle2">Treatments</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="q-pa-sm bg-grey-2 q-mb-sm rounded" style="position: relative; border-radius: 5px;" v-for="(treatment, key) in local_treatments" :key="`treatment-${key + 1}`">
                <div class="absolute flex items-center justify-center" style="top: -.5rem; right: -.5rem;" v-if="!is_readonly">
                    <q-btn color="red-14" icon="delete" size="sm" round
                        @click="removeInformation('treatment', key, treatment.id)" v-if="local_treatments.length > 1">
                        <q-tooltip class="bg-grey-14 text-white">Remove Treatment</q-tooltip>
                    </q-btn>
                </div>
                <div class="row q-mb-sm rounded">
                    <div class="col-12 col-md-6 q-pa-sm medicine-generic-name-select">
                        <q-select :options="local_medicine_generic_names"
                            ref="generic_select"
                            :readonly="is_readonly"
                            label="Generic Name"
                            outlined
                            v-model="treatment.medicine_generic"
                            use-input
                            @filter="handleFilterGenericMedicineNames"
                            clearable
                        >
                            <template #no-option="props">
                                <q-item class="text-uppercase">
                                    <q-item-section class="text-grey-6">
                                        <div class="q-gutter-sm">
                                            <p>No matching generic medicine name</p> 
                                            <q-btn :label="`Click this to add '${props.inputValue}'`" color="primary" icon="add" @click="saveOption(props.inputValue, key)" /></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-select>
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <q-input type="text" :readonly="is_readonly" label="Brand" outlined v-model="treatment.brand"
                            :rules="[]" />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <q-input type="text" :readonly="is_readonly" label="Dose" outlined v-model="treatment.dose"
                            :rules="[]" />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <q-input type="text" :readonly="is_readonly" label="Reminder" outlined v-model="treatment.reminder"
                            :rules="[]" />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <q-input type="text" :readonly="is_readonly" label="Duration Days" outlined v-model="treatment.duration"
                            :rules="[]" />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <q-input type="text" :readonly="is_readonly" label="Quantity" outlined v-model="treatment.quantity"
                            :rules="[]" />
                    </div>
                </div>
            </div>
            <div>
                <q-btn color="primary" label="Add Treatment" icon="add" size="sm" outline v-if="!is_readonly"
                    @click="addTreatment" />
            </div>
        </q-card-section>
    </q-card>
</template>
<script setup>
import { ref, watch, getCurrentInstance } from 'vue';
import { useMedicineStore } from 'stores/medicine';
const medicineStore = useMedicineStore();
import { Notify } from 'quasar';

const { proxy } = getCurrentInstance();
const helpers = proxy.helpers;

const { saveMedicineGenericName } = medicineStore;

const props = defineProps({
    treatments: {
        type: Array,
        default: () => []
    },
    is_readonly: {
        type: Boolean,
        default: false
    },
    medicine_generic_names:{
        type: Array,
        default: () => []
    },
});
const emit = defineEmits(['update:treatments', 'remove', 'update:medicine_generic_names']);

const local_treatments = ref([...props.treatments]);
const all_medicine_generic_names = ref([...props.medicine_generic_names]);
const local_medicine_generic_names = ref([...props.medicine_generic_names]);
const generic_select = ref(null);
const default_form_value = { 
    id: null,
    medicine_generic: '',
    medicine_brand: '',
    dose: '',
    reminder: '',
    duration: '',
    quantity: '',
}

watch(() => props.treatments, (newVal) => {
    local_treatments.value = [...newVal];
}, { deep: true });

watch(() => props.medicine_generic_names, (newVal) => {
    all_medicine_generic_names.value = [...newVal];
    local_medicine_generic_names.value = [...newVal];
}, { deep: true });

const addTreatment = () => {
    local_treatments.value.push({ ...default_form_value });
    emit('update:treatments', local_treatments.value);
};

const removeInformation = (type, index, id) => {
    local_treatments.value.splice(index, 1);
    emit('update:treatments', local_treatments.value);
    if (id) {
        emit('remove', type, index, id);
    }
};

const handleFilterGenericMedicineNames = (val, update) => {
    update(() => {
        if (val === '') {
            local_medicine_generic_names.value = [...all_medicine_generic_names.value];
        } else {
            local_medicine_generic_names.value = [...all_medicine_generic_names.value].filter(name => name.toLowerCase().includes(val.toLowerCase()));
        }
    });
};

const saveOption = (inputVal, key) => {
    if (inputVal && inputVal.trim() !== '') {
        const newOption = inputVal.trim();
        if(newOption) {
            let formattedOptions = [...all_medicine_generic_names.value, newOption];
            all_medicine_generic_names.value = formattedOptions.sort();
            local_treatments.value[key].medicine_generic = newOption.toUpperCase();
            emit('update:medicine_generic_names', all_medicine_generic_names.value);
            emit('update:treatments', local_treatments.value);
            saveOptionToDatabase(newOption.toUpperCase()).then(() => {
                if(generic_select.value) {
                    generic_select.value[0].updateInputValue('');
                }
            })
        }
    }
};

const saveOptionToDatabase = async (inputVal) => {
    try {
        if (inputVal && inputVal.trim() !== '') {
            const newOption = inputVal.trim();
            await saveMedicineGenericName({ name: newOption });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to save medicine generic name.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    }
}

</script>

<style>
.medicine-generic-name-select .q-field__input {
    text-transform: uppercase!important;
}
</style>