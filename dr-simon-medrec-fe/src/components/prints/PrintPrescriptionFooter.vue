<template>
    <div class="print-footer flex column q-gutter-md">
        <div class="flex column footer-right-content">
            <u>Simon John Eric T. Flores M.D.</u>
            <div>License No. 0092579</div>
            <div class="flex">
                <div>PTR No.</div>
                <div class="grow-1" style="border-bottom: 1px solid black; padding-left: 5px;" v-if="!settings"></div>
                <div v-else class="q-ml-xs">{{settings?.ptr_number}}</div>
            </div>
            <div class="flex">
                <div>S2 No.</div>
                <div class="grow-1" style="border-bottom: 1px solid black;"></div>
            </div>
        </div>
        <div>
            Follow up Schedule: 
        </div>
    </div>
</template>
<script setup>
import { getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();
const settings = proxy.$settings?.data || null;

</script>
<style scoped>
.grow-1 {
    flex-grow: 1;
}
.footer-right-content {
    align-self: flex-end;
    width: 2in;
}

.print-footer {
    font-size: 10pt;
    font-weight: 500;
    line-height: 11pt;
}
</style>