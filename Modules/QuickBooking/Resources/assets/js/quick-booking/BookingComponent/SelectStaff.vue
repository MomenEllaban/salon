<template>
    <div class="card-list-data">
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" v-if="!IS_LOADER">
          <template v-for="(item, index) in employeeList" :key="`items-${index}`">
              <div class="col">
                <div class="iq-widget">
                    <input type="radio" :id="item.first_name + item.id" v-model="employee_id" :value="item.id" name="radio" class="btn-check" @change="onChange"/>
                    <label :for="item.first_name + item.id" class="d-block w-100">
                        <div class="card iq-card iq-staff-box">
                            <div class="card-body text-center p-0">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <img :src="item.profile_image" class="avatar-90 rounded-circle" alt="3">
                                </div>
                                <h5 class="m-0">{{ item.full_name }}</h5>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
          </template>
      </div>
      <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
        <div class="col" v-for="index in 6" :key="index">
          <div class="iq-widget card iq-card card-skeleton">
            <div class="card-body text-center p-0">
                <div class="skeleton skeleton-image avatar-90 rounded-circle m-auto">
                </div>
                <h5 class="skeleton skeleton-title w-10 mt-3 mb-0"></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="h-100 w-75 d-flex align-items-center justify-content-center mx-auto" v-if="employeeList.length == 0">
        We apologize for the inconvenience, but currently, there are no staff members available at this particular salon branch.
      </div>
    </div>
    <div class="card-footer">
      <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">{{ $t('quick_booking.lbl_back') }}</button>
      <button type="button" v-if="employeeList.length > 0 && wizardNext" class="btn btn-primary iq-text-uppercase" :disabled="employee_id !== null ? false : true" @click="nextTabChange(wizardNext)">{{ $t('quick_booking.lbl_next') }}</button>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'

// Select Options List Request
import { EMPLOYEE_LIST } from '@/vue/constants/quick_booking'
import {useQuickBooking} from '../../store/quick-booking'
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

const {  listingRequest } = useRequest()
const store = useQuickBooking()

const employeeList = ref([]);
const employee_id = ref(null)
const IS_LOADER = ref(true)
const getStaffs = () => {
  IS_LOADER.value = true
  const data = {
    branch_id: store.booking.branch_id,
    service_id: store.booking.services[0].service_id,
    start_date_time: store.booking.services[0].start_date_time
  }
  if(data.branch_id !== null || data.service_id !== null || data.start_date_time) {
    listingRequest({ url: EMPLOYEE_LIST, data: data}).then((res) => {
      IS_LOADER.value = false
      employeeList.value = res.data
    })
  }
}
watch(() => store.booking.services[0].service_id,() => {
  getStaffs()
}, {deep: true})

watch(() => employee_id.value, (value) => {
  store.updateBookingEmployeeValues(value)
  store.updateBookingValues({key: 'employee_id',value: value})
})
watch(() => store.bookingResponse, (value) => {
  resetData()
}, {deep: true})
// On Change Next
const onChange = () => {
    emit('tab-change', props.wizardNext)
}
const nextTabChange = (val) => (emit('tab-change', val))
const prevTabChange = (val) => {
  resetData()
  emit('tab-change', val)
}
const resetData = () => {
  employee_id.value = null
}
</script>

<style scoped>

    .card-list-data {
        position: relative;
        padding-top: 10px;
        padding-right: 10px;
    }
    .iq-staff-box {
        padding: 30px 15px;
        border: 1px solid #CCCDCD;
        background: var(--bs-white);
        box-shadow: none;
    }
    .iq-staff-box::after {
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
    }

    .iq-widget .btn-check:checked + label .iq-staff-box {
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), 0.1);
    }

    .iq-widget .btn-check:checked + label .iq-staff-box::after {
        opacity: 1;
    }

    .iq-staff-box .iq-staff-desc {
        font-weight: 500;
        font-size: 12px;
        line-height: 18px;
        letter-spacing: 0.12em;
    }
</style>
