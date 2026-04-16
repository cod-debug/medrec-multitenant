<template>
    <div class="text-center">
        <div class="header-name">SIMON JOHN ERIC T. FLORES M.D., FPSO-HNS</div>
        <div class="header-subtitle">Fellowship - Philippine Society Of Otolaryngology - Head and Neck Surgery</div>
        <div class="row clinic-info text-left">
            <div class="col-6 border-right">
                <div class="title">Clinic Hours</div>
                <div class="value">MONDAY - SATURDAY: 8:00AM onwards</div>
                <div class="title">Tel. 0935-560-3056 / 420-2217</div>
                <div class="title">Siliman University Medical Center</div>
            </div>
            <div class="col-6">
                <div class="clinic-address-title">Clinic Address</div>
                <div class="clinic-address-value">Room 217, Siliman University Medical Center, Laguna Road, Dumaguete City 6200, Negros Oriental, Philippines</div>
                <div class="hospital-affiliation-title">Hospital Affiliation</div>
                <div class="hospital-affiliation-value">Siliman University Medical Center</div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.header-name {
    font-weight: bold;
    font-size: 1.5em;
}

.header-subtitle {
    font-size: 9.5pt;
    font-weight: 500;
}

.clinic-info {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

.clinic-info > div {
    padding: 5px .5rem 5px 1rem;
}

.clinic-info .title{
    font-weight: bold;
    font-size: 10pt;
}

.clinic-info .value{
    font-size: 9pt;
}

.border-right {
    border-right: 1px solid black;
}
.clinic-address-title,
.hospital-affiliation-title {
    font-weight: bold;
    font-size: 9pt;
}
.clinic-address-value,
.hospital-affiliation-value {
    font-size: 8pt;
    line-height: 8pt;
}
</style>