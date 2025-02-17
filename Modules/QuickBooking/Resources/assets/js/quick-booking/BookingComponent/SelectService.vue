<template>
  <div class="card-list-data">
    <template v-for="(item, index) in serviceList" :key="`items-${index}`">
        <h5 class="text-primary fw-bold mb-4" v-if="!IS_LOADER">{{ item.category_name }}</h5>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3" v-if="!IS_LOADER">
            <template v-for="(serviceItem, index) in item.services" :key="`services-${index}`">
                <div class="iq-widget">
                    <input type="radio" :id="serviceItem.name + serviceItem.id" v-model="service_id" :value="serviceItem.id" name="radio" class="btn-check" @change="onChange"/>
                    <label :for="serviceItem.name + serviceItem.id" class="d-block w-100">
                        <div class="card iq-service-box text-center">
                            <div class="card-body">
                                <div>
                                    <h5 class="mb-2">{{ serviceItem.name }}</h5>
                                    <p class="m-0 mt-3">{{ serviceItem.duration_min }} min</p>
                                </div>
                                <div class="service-price mt-3">
                                    <b>{{ formatCurrencyVue(serviceItem.default_price) }}</b>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </template>
        </div>
    </template>
    <div v-if="serviceList.length == 0 && IS_LOADER">
      <h5 class="skeleton skeleton-title w-25 ms-0 mb-4"></h5>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3" v-if="IS_LOADER">
        <div class="col" v-for="index in 9" :key="index">
          <div class="iq-widget card card-skeleton text-center">
            <div class="card-body text-center pt-5 pb-5">
                <h5 class="skeleton skeleton-title w-100 mb-2"></h5>
                <p class="skeleton skeleton-text w-50 m-auto mt-3"></p>
                <div class="skeleton skeleton-badge mt-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="serviceList.length == 0 && !IS_LOADER" class="h-100 w-75 d-flex align-items-center justify-content-center">
      We apologize for any inconvenience caused. Unfortunately, the selected salon branch does not offer the service you are looking for at the moment.
    </div>
  </div>
  <div class="card-footer">
    <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">{{ $t('quick_booking.lbl_back') }}</button>
    <button type="button" v-if="wizardNext" class="btn btn-primary iq-text-uppercase" :disabled="service_id !== null ? false : true" @click="nextTabChange(wizardNext)">{{ $t('quick_booking.lbl_next') }}</button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'

// Select Options List Request
import { SERVICE_LIST } from '@/vue/constants/quick_booking'
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
const formatCurrencyVue = (value) => {
  if(window.currencyFormat !== undefined && value) {
    return window.currencyFormat(value)
  }
  return value
}
const {  listingRequest } = useRequest()
const store = useQuickBooking()
const IS_LOADER = ref(true)

const serviceList = ref([]);
const groupData = (data) => {
    return data.reduce((result, obj) => {
        const key = obj.category_id;
        if (!result[key]) {
            result[key] = {
                category_id: obj.category_id,
                category_name: obj.category_name,
                services: []
            };
        }
        result[key].services.push({...obj});
        return result;
    }, {})
}
const getBranch = () => {
  IS_LOADER.value = true
  const data = {
    branch_id: store.booking.branch_id,
    category_id: store.booking.category_id
  }
  if(store.booking.branch_id !== null || store.booking.category_id !== null) {
    listingRequest({ url: SERVICE_LIST, data: data}).then((res) => {
      IS_LOADER.value = false
      serviceList.value = groupData(res.data)
    })
  }
}
watch(() => store.booking.branch_id, () => {
  getBranch()
})
const service_id = ref(null)
watch(() => service_id.value, (value) => {
    let categories = Object.values(serviceList.value)
    categories.forEach(category => {
    if (Array.isArray(category.services)) {
      const foundService = category.services.find(service => service.id === value);
      if (foundService) {
        store.updateBookingValues({
          key: 'services',
          value: [{
            duration_min: foundService.duration_min,
            employee_id: null,
            service_id: foundService.id,
            service_price: foundService.default_price,
            start_date_time: store.booking.start_date_time
          }]
        });
      }
    }
  });
}, {deep: true})
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
  store.updateBookingValues({
    key: 'services',
    value: [{
      duration_min: null,
      employee_id: store.booking.employee_id,
      service_id: null,
      service_price: null,
      start_date_time: store.booking.start_date_time
    }]
  });
  service_id.value = null
}
</script>

<style scoped>

    .card-list-data {
        position: relative;
        padding-top: 10px;
        padding-right: 10px;
    }
    .card.iq-service-box {
        cursor: pointer;
        border: 1px solid #ECECEC;
        background: var(--bs-white);
        border-radius: 10px;
        transition: all 0.5s ease-in-out;

    }
    .card.iq-service-box .card-body {
        padding: 30px 15px;
    }

    .card.iq-service-box:hover {
        border-color: var(--bs-primary);
        transform: translateY(-5px);
    }

    .iq-service-box::after {
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

    .iq-widget .btn-check:checked + label .iq-service-box::after {
        opacity: 1;
    }

    .service-price {
        color: #19235A;
        background: #FCF2E3;
        border-radius: 26px;
        padding: 6px 16px;
        display: inline-block;
    }

    .iq-widget .btn-check:checked + label .iq-service-box {
        background: var(--bs-primary);
        color: var(--bs-white);
    }

    .iq-widget .btn-check:checked + label .iq-service-box h5 {
        color: var(--bs-white);
    }
    .iq-widget .btn-check:checked + label .iq-service-box .service-price {
        background: var(--bs-white);
    }
</style>
