<template>
    <div class="card-list-data">
        <div class="row row-cols-1 row-cols-md-2" v-if="!IS_LOADER">
            <div class="col" v-for="(item, index) in branchList" :key="item">
                <div class="iq-widget">
                    <input type="radio" :id="item.name + item.id" name="radio" :value="item.id" v-model="branch_id" class="btn-check" @change="onChange"/>
                    <label :for="item.name + item.id" class="d-block w-100">
                        <div class="card iq-branch-box">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="branch-image">
                                        <img :src="item.feature_image" class="avatar-70 rounded-circle" alt="feature-image" loading="lazy">
                                    </div>
                                    <div class="branch-content mt-3 mt-sm-0">
                                        <h4 class="mb-1">{{ item.name || 'No Branch' }}</h4>
                                        <p class="m-0">{{ item.address.country || '-' }} / {{ item.address.postal_code || '000000' }}</p>
                                    </div>
                                </div>
                                <hr>
                                <ul class="iq-contact-detail list-inline p-0 m-0">
                                    <li class="d-flex align-items-center justify-content-center mb-3">
                                        <svg class="text-dark" fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5317 12.4724C15.5208 16.4604 16.4258 11.8467 18.9656 14.3848C21.4143 16.8328 22.8216 17.3232 19.7192 20.4247C19.3306 20.737 16.8616 24.4943 8.1846 15.8197C-0.493478 7.144 3.26158 4.67244 3.57397 4.28395C6.68387 1.17385 7.16586 2.58938 9.61449 5.03733C12.1544 7.5765 7.54266 8.48441 11.5317 12.4724Z" fill="currentColor" />
                                        </svg>
                                        <p class="ms-2 mb-0">{{ item.contact_number || '-' }}</p>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-center">
                                        <svg class="text-dark" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M22 15.94C22 18.73 19.76 20.99 16.97 21H16.96H7.05C4.27 21 2 18.75 2 15.96V15.95C2 15.95 2.006 11.524 2.014 9.298C2.015 8.88 2.495 8.646 2.822 8.906C5.198 10.791 9.447 14.228 9.5 14.273C10.21 14.842 11.11 15.163 12.03 15.163C12.95 15.163 13.85 14.842 14.56 14.262C14.613 14.227 18.767 10.893 21.179 8.977C21.507 8.716 21.989 8.95 21.99 9.367C22 11.576 22 15.94 22 15.94Z" fill="currentColor"></path>
                                            <path d="M21.4759 5.67351C20.6099 4.04151 18.9059 2.99951 17.0299 2.99951H7.04988C5.17388 2.99951 3.46988 4.04151 2.60388 5.67351C2.40988 6.03851 2.50188 6.49351 2.82488 6.75151L10.2499 12.6905C10.7699 13.1105 11.3999 13.3195 12.0299 13.3195C12.0339 13.3195 12.0369 13.3195 12.0399 13.3195C12.0429 13.3195 12.0469 13.3195 12.0499 13.3195C12.6799 13.3195 13.3099 13.1105 13.8299 12.6905L21.2549 6.75151C21.5779 6.49351 21.6699 6.03851 21.4759 5.67351Z" fill="currentColor"></path>
                                        </svg>
                                        <p class="ms-2 mb-0">{{ item.contact_email || '-' }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div v-else class="row row-cols-1 row-cols-md-2">
            <div class="col" v-for="index in 4" :key="index">
                <div class="iq-widget card card-skeleton">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="skeleton skeleton-image avatar-70 m-auto rounded-circle">
                            </div>
                            <div class="mt-3 mt-sm-0">
                                <h4 class="skeleton skeleton-title w-50 mb-3"></h4>
                                <p class="skeleton skeleton-text w-100 m-0"></p>
                            </div>
                        </div>
                        <hr>
                        <ul class="list-inline p-0 m-0">
                            <li class="skeleton skeleton-text w-75 m-auto mb-3"></li>
                            <li class="skeleton skeleton-text w-100 m-auto"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-100 w-75 d-flex align-items-center justify-content-center" v-if="branchList.length == 0 && !IS_LOADER">
          We apologize, but currently, there are no available branches for booking appointments.
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">Back</button>
      <button type="button" class="btn btn-primary iq-text-uppercase" :disabled="branch_id !== null ? false : true" v-if="wizardNext" @click="nextTabChange(wizardNext)">{{ $t('quick_booking.lbl_next') }}</button>
    </div>
</template>
<script setup>
// Library Import
import { ref, onMounted, watch } from 'vue'

// Custom Hooks Import
import { useRequest } from '@/helpers/hooks/useCrudOpration'

// Store Import
import {useQuickBooking} from '../../store/quick-booking'

// URL Constant Import
import { BRANCH_LIST } from '@/vue/constants/quick_booking'

// Props & Emits
const props = defineProps({
  wizardNext: {
    default: '',
    type: [String, Number]
  },
  wizardPrev: {
    default: '',
    type: [String, Number]
  },
})
const emit = defineEmits(['tab-change', 'onReset'])

// List, Store, Update, Get Data Request Hook
const {  listingRequest } = useRequest()

// Variables
const branchList = ref([]);
const branch_id = ref(null)
const IS_LOADER = ref(true)

// Mounted
onMounted(() => {
    getBranch()
})

// Functions
const getBranch = () => {
    IS_LOADER.value = true
    listingRequest({ url: BRANCH_LIST}).then((res) => {
        IS_LOADER.value = false
        branchList.value = res.data
  })
}

// Store
const store = useQuickBooking()

// On Change Next
const onChange = () => {
    emit('tab-change', props.wizardNext)
}
// Next & Prev Function
const nextTabChange = (val) => (emit('tab-change', val))
const prevTabChange = (val) => {
    resetData()
    emit('tab-change', val)
}

// Reset Data Function
const resetData = () => {
    branch_id.value = null
}

// Watch
watch(() => branch_id.value, (value) => {
  store.updateBookingValues(
    {key: 'branch_id',value: value}
  )
}, {deep: true})
watch(() => store.bookingResponse, (value) => {
  resetData()
}, {deep: true})

</script>
<!-- Scoped styles it will work only on this component-->
<style scoped>
    .card-list-data {
        position: relative;
        padding-top: 10px;
        padding-right: 10px;
    }
    .iq-branch-box {
        cursor: pointer;
        border: 1px solid var(--bs-border-color);
        transition: all 0.5s ease-in-out;
        animation: fade-in 0.75s ease-in-out both;
    }

    .iq-branch-box:hover {
        border-color: var(--bs-primary);
        transform: translateY(-5px);
    }

    .iq-branch-box::after {
        position: absolute;
        content: "";
        background: var(--bs-primary) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
        height: 23px;
        width: 23px;
        border: 2px solid var(--bs-white);
        top: -7px;
        left: auto;
        right: -7px;
        border-radius: 100%;
        opacity: 0;
        transition: all 0.5s ease-in-out;
    }

    .iq-widget .btn-check:checked + label .iq-branch-box {
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), 0.1);
    }

    .iq-widget .btn-check:checked + label .iq-branch-box::after {
        opacity: 1;
    }

    .iq-branch-box .card-body {
        padding: 20px;
    }
    .iq-contact-detail p {
        font-size: 14px;
    }
    .iq-branch-for {
        position: absolute;
        top: 10px;
        left: auto;
        right: 15px;
    }
</style>
