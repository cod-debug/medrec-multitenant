<template>
    <q-card class="q-mt-md" style="height: 100%;">
        <q-card-section class="bg-blue-4 text-white flex items-center justify-between">
            <div class="text-subtitle2">Treatments</div>
            <div><q-btn color="primary" label="Print" icon="print" @click="$emit('print')" /></div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="q-pa-sm bg-grey-2 q-mb-sm rounded" style="position: relative; border-radius: 5px;" v-for="(prescription, key) in local_prescriptions" :key="`prescription-${key + 1}`">
                <div class="absolute flex items-center justify-center" style="top: -.5rem; right: -.5rem;" v-if="!is_readonly">
                    <q-btn color="red-14" icon="delete" size="sm" round
                        @click="removeInformation('prescription', key, prescription.id)" v-if="local_prescriptions.length > 1">
                        <q-tooltip class="bg-grey-14 text-white">Remove Treatment</q-tooltip>
                    </q-btn>
                </div>
                <div class="row q-mb-sm rounded">
                    <div class="col-12 q-pa-sm">
                        <div class="q-mt-md" v-if="!is_readonly">
                            <q-toggle
                                v-model="prescription.is_remarks_only"
                                color="primary"
                                label="Remarks Only"
                            />
                        </div>
                    </div>
                    <div class="col-12 q-pa-sm" v-if="prescription.is_remarks_only">
                        <q-input type="textarea" :readonly="is_readonly" label="Remarks" outlined v-model="prescription.remarks"
                            :rules="[]" />
                    </div>
                    <template v-if="!prescription.is_remarks_only">
                        <div class="col-12 col-md-6 q-pa-sm medicine-generic-name-select">
                            <q-select :options="local_medicine_generic_names"
                                ref="generic_select"
                                :readonly="is_readonly"
                                label="Generic Name"
                                outlined
                                v-model="prescription.medicine_generic"
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
                            <q-select :options="brand_options"
                                ref="brand_select"
                                label="Brand"
                                outlined
                                v-model="prescription.medicine_brand"
                                :readonly="is_readonly"
                                use-input
                                @filter="handleFilterBrands"
                                @keyup.enter="($event) => saveBrandOption($event.target.value, key)"
                                clearable>
                                <template #no-option="props">
                                    <q-item class="text-uppercase">
                                        <q-item-section class="text-grey-6">
                                            <div class="q-gutter-sm">
                                                <p>No matching brand name</p> 
                                                <q-btn :label="`Click this to add '${props.inputValue}'`" color="primary" icon="add" @click="saveBrandOption(props.inputValue, key)" />
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-select :options="dosage_options"
                                ref="dose_select"
                                label="Dose"
                                outlined
                                v-model="prescription.dosage"
                                :readonly="is_readonly"
                                use-input
                                @filter="handleFilterDosages"
                                @keyup.enter="($event) => saveDoseOption($event.target.value, key)"
                                clearable>
                                <template #no-option="props">
                                    <q-item class="text-uppercase">
                                        <q-item-section class="text-grey-6">
                                            <div class="q-gutter-sm">
                                                <p>No matching dose</p> 
                                                <q-btn :label="`Click this to add '${props.inputValue}'`" color="primary" icon="add" @click="saveDoseOption(props.inputValue, key)" />
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input type="text" :readonly="is_readonly" label="Reminder" outlined v-model="prescription.reminder"
                                :rules="[]" />
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input type="text" :readonly="is_readonly" label="Duration Days" outlined v-model="prescription.duration"
                                :rules="[]"    
                            />
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <div class="row">
                                <div class="col-6">
                                    <q-select :options="quantity_options"
                                    ref="preparation_select"
                                    v-model="prescription.quantity_prefix"
                                    label="Preparation"
                                    outlined
                                    :readonly="is_readonly"
                                    clearable
                                    use-input
                                    @filter="handleFilterPreparationMethods"
                                    @keyup.enter="($event) => savePreparationOption($event.target.value, key)"
                                >
                                    <template #no-option="props">
                                        <q-item class="text-uppercase">
                                            <q-item-section class="text-grey-6">
                                                <div class="q-gutter-sm">
                                                    <p>No matching preparation</p> 
                                                    <q-btn :label="`Click this to add '${props.inputValue}'`" color="primary" icon="add" @click="savePreparationOption(props.inputValue, key)" />
                                                </div>
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                    </q-select>
                                </div>
                                <div class="col-6">
                                    <q-input type="text" :readonly="is_readonly" label="Quantity" outlined v-model="prescription.quantity"
                                        :rules="[]">
                                    </q-input>
                                </div>
                            </div>
                        </div>
                    </template>
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
import { Notify } from 'quasar';

const medicineStore = useMedicineStore();

const { proxy } = getCurrentInstance();
const helpers = proxy.helpers;

const { 
    saveMedicineGenericName,
    createMedicineBrand,
    createMedicineDose, 
    createMedicinePreparation
} = medicineStore;

const props = defineProps({
    prescriptions: {
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
    medicine_brand_names: {
        type: Array,
        default: () => []
    },
    medicine_dosages: {
        type: Array,
        default: () => []
    },
    medicine_preparation_methods: {
        type: Array,
        default: () => []
    },
});

const brand_options = ref([...props.medicine_brand_names]);
const dosage_options = ref([...props.medicine_dosages]);
const quantity_options = ref([...props.medicine_preparation_methods]);

const emit = defineEmits(['update:prescriptions', 
    'remove',
    'update:medicine_generic_names',
    'update:medicine_brand_names',
    'update:medicine_dosages',
    'update:medicine_preparation_methods',
    'print'
]);

const local_prescriptions = ref([...props.prescriptions]);
const all_medicine_generic_names = ref([...props.medicine_generic_names]);
const local_medicine_generic_names = ref([...props.medicine_generic_names]);

const generic_select = ref(null);
const brand_select = ref(null);
const dose_select = ref(null);
const preparation_select = ref(null);
const default_form_value = { 
    id: null,
    medicine_generic: '',
    medicine_brand: '',
    dosage: '',
    reminder: '',
    duration: '',
    quantity: '',
    quantity_prefix: '',
    remarks: '',
    is_remarks_only: false
}

const addTreatment = () => {
    local_prescriptions.value.push({ ...default_form_value });
    emit('update:prescriptions', local_prescriptions.value);
};

const removeInformation = (type, index, id) => {
    local_prescriptions.value.splice(index, 1);
    emit('update:prescriptions', local_prescriptions.value);
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

const handleFilterBrands = (val, update) => {
    update(() => {
        if (val === '') {
            brand_options.value = [...props.medicine_brand_names];
        } else {
            brand_options.value = [...props.medicine_brand_names].filter(name => name.toLowerCase().includes(val.toLowerCase()));
        }
    });
};

const handleFilterDosages = (val, update) => {
    update(() => {
        if (val === '') {
            dosage_options.value = [...props.medicine_dosages];
        } else {
            dosage_options.value = [...props.medicine_dosages].filter(name => name.toLowerCase().includes(val.toLowerCase()));
        }
    });
};

const handleFilterPreparationMethods = (val, update) => {
    update(() => {
        if (val === '') {
            quantity_options.value = [...props.medicine_preparation_methods];
        } else {
            quantity_options.value = [...props.medicine_preparation_methods].filter(name => name.toLowerCase().includes(val.toLowerCase()));
        }
    });
};

const saveOption = (inputVal, key) => {
    if (inputVal && inputVal.trim() !== '') {
        const newOption = inputVal.trim();
        if(newOption) {
            let formattedOptions = [...all_medicine_generic_names.value, newOption];
            all_medicine_generic_names.value = formattedOptions.sort();
            local_prescriptions.value[key].medicine_generic = newOption.toUpperCase();
            emit('update:medicine_generic_names', all_medicine_generic_names.value);
            emit('update:prescriptions', local_prescriptions.value);
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

const saveBrandOption = async (inputVal, key) => {
    if (inputVal && inputVal.trim() !== '') {
        const newOption = inputVal.trim();
        if(newOption) {
            try {
                await createMedicineBrand({ name: newOption });
                
                let formattedOptions = [...props.medicine_brand_names, newOption];
                brand_options.value = formattedOptions.sort();
                local_prescriptions.value[key].medicine_brand = newOption;
                emit('update:medicine_brand_names', formattedOptions.sort());
                emit('update:prescriptions', local_prescriptions.value);
                
                if(brand_select.value && brand_select.value[0]) {
                    brand_select.value[0].updateInputValue('');
                }
                
                Notify.create({
                    message: 'Brand saved successfully.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'green'
                });
            } catch (e) {
                helpers.showDebugMessage(e);
                Notify.create({
                    message: 'Failed to save medicine brand.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'red-14'
                });
            }
        }
    }
};

const saveDoseOption = async (inputVal, key) => {
    if (inputVal && inputVal.trim() !== '') {
        const newOption = inputVal.trim();
        if(newOption) {
            try {
                await createMedicineDose({ name: newOption });
                
                let formattedOptions = [...props.medicine_dosages, newOption];
                dosage_options.value = formattedOptions.sort();
                local_prescriptions.value[key].dosage = newOption;
                emit('update:medicine_dosages', formattedOptions.sort());
                emit('update:prescriptions', local_prescriptions.value);
                
                if(dose_select.value && dose_select.value[0]) {
                    dose_select.value[0].updateInputValue('');
                }
                
                Notify.create({
                    message: 'Dose saved successfully.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'green'
                });
            } catch (e) {
                helpers.showDebugMessage(e);
                Notify.create({
                    message: 'Failed to save medicine dose.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'red-14'
                });
            }
        }
    }
};

const savePreparationOption = async (inputVal, key) => {
    if (inputVal && inputVal.trim() !== '') {
        const newOption = inputVal.trim();
        if(newOption) {
            try {
                await createMedicinePreparation({ name: newOption });
                
                let formattedOptions = [...props.medicine_preparation_methods, newOption];
                quantity_options.value = formattedOptions.sort();
                local_prescriptions.value[key].quantity_prefix = newOption;
                emit('update:medicine_preparation_methods', formattedOptions.sort());
                emit('update:prescriptions', local_prescriptions.value);
                
                if(preparation_select.value && preparation_select.value[0]) {
                    preparation_select.value[0].updateInputValue('');
                }
                
                Notify.create({
                    message: 'Preparation saved successfully.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'green'
                });
            } catch (e) {
                helpers.showDebugMessage(e);
                Notify.create({
                    message: 'Failed to save medicine preparation.',
                    position: 'top-right',
                    closeBtn: "X",
                    timeout: 3000,
                    color: 'red-14'
                });
            }
        }
    }
};



watch(() => props.prescriptions, (newVal) => {
    local_prescriptions.value = [...newVal];
}, { deep: true });

watch(() => props.medicine_generic_names, (newVal) => {
    all_medicine_generic_names.value = [...newVal];
    local_medicine_generic_names.value = [...newVal];
}, { deep: true });

watch(() => props.medicine_brand_names, (newVal) => {
    brand_options.value = [...newVal];
}, { deep: true });

watch(() => props.medicine_dosages, (newVal) => {
    dosage_options.value = [...newVal];
}, { deep: true });

watch(() => props.medicine_preparation_methods, (newVal) => {
    quantity_options.value = [...newVal];
}, { deep: true });

</script>

<style>
.medicine-generic-name-select .q-field__input {
    text-transform: uppercase!important;
}
</style>