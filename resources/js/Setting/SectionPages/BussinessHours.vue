<template>
  <form @submit="formSubmit" class="bussiness-hour">
    <div class="col-md-12 d-flex justify-content-between">
      <CardTitle :title="$t('setting_sidebar.lbl_bussiness')" icon="fa-solid fa-clock"></CardTitle>
    </div>
    <div class="form-group col-12" v-if="branch.options.length > 1">
      <label class="form-label">{{ $t('setting_business_hours.lbl_name') }}<span class="text-danger">*</span></label>
      <Multiselect id="branch_id" v-model="branch_id" v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" class="form-group"></Multiselect>
      <span class="text-danger">{{ errors.branch_id }}</span>
    </div>
    <ul class="data-scrollbar list-group list-group-flush">
      <li v-for="(day, index) in weekdays" class="form-group col-md-12 list-group-item" :key="++index">
        <div class="form-group col-md-12 gap-1">
          <h4 class="text-capitalize">{{ index }}. {{ day.day }} <i v-if="index === 1" aria-hidden="true" @click="handleCopy"></i></h4>

          <div class="col-md-12 row row-cols-3">
            <flat-pickr id="start_time" class="form-control" v-model="day.start_time" :config="config" :disabled="day.is_holiday ? true : false" :class="{ background_colour: day.is_holiday }"></flat-pickr>
            <flat-pickr id="end_time" class="form-control" v-model="day.end_time" :config="config" :disabled="day.is_holiday ? true : false" :class="{ background_colour: day.is_holiday }"></flat-pickr>

            <div class="form-group">
              <div class="d-flex gap-1">
                <div class="form-check">
                  <input class="form-check-input" :value="1" name="is_holiday" :id="`${index}-dayoff`" type="checkbox" :true-value="1" :false-value="0" v-model="day.is_holiday" />
                </div>
                <label class="form-label" :for="`${index}-dayoff`">{{ $t('setting_business_hours.lbl_add_day_off') }}</label>
              </div>
            </div>
          </div>
          <template v-if="!day.is_holiday">
            <div v-for="(input, index) in day.breaks" :key="index" class="form-group">
              <h6>{{ $t('setting_business_hours.lbl_break') }}</h6>
              <div class="col-md-12 row row-cols-3">
                <flat-pickr id="start_break" class="form-control" v-model="input.start_break" :config="config"></flat-pickr>
                <flat-pickr id="end_break" class="form-control" v-model="input.end_break" :config="config"></flat-pickr>
                <div>
                  <a class="btn btn-primary" @click="deleteInputField(day, index)">{{ $t('messages.remove') }}</a>
                </div>
              </div>
            </div>
            <div>
              <a @click="addInputField(day)" class="clickable-text">
                <h6><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ $t('setting_business_hours.lbl_add_break') }}</h6>
              </a>
            </div>
          </template>
        </div>
      </li>
    </ul>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { BRANCH_LIST } from '@/vue/constants/branch'
import { LISTING_URL, STORE_URL } from '@/vue/constants/busssiness_hours'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useSelect } from '@/helpers/hooks/useSelect'
import FlatPickr from 'vue-flatpickr-component'
import { useForm, useField } from 'vee-validate'
import moment from 'moment'
import * as yup from 'yup'
import SubmitButton from './Forms/SubmitButton.vue'

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false
})

const IS_SUBMITED = ref(false)

const config = ref({
  dateFormat: 'H:i:S', // Use lowercase 'h' for 12-hour format, 'K' for AM/PM indicator
  //time_24hr: false, // Use 12-hour format
  altInput: true,
  altFormat: 'h:i K',
  enableTime: true,
  noCalendar: true,
  defaultHour: '09', // Update default hour to 9
  defaultMinute: '00',
  defaultSeconds: '00',
  static: true
})

const { storeRequest, getRequest } = useRequest()

const validationSchema = yup.object({
  branch_id: yup.number().required()
})

const { handleSubmit, errors } = useForm({ validationSchema })

const { value: branch_id } = useField('branch_id')

const branch = ref({ options: [], list: [] })

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

onMounted(() => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
    if (data.options.length === 1) {
      branch_id.value = data.options[0].value
      branchSelect()
    }
  })
})

const defaultData = () => {
  return [
    { day: 'monday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'tuesday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'wednesday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'thursday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'friday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'saturday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] },
    { day: 'sunday', start_time: moment().set({ hour: 9, minute: 0, second: 0 }).format('HH:mm'), end_time: moment().set({ hour: 18, minute: 0, second: 0 }).format('HH:mm'), is_holiday: false, breaks: [] }
  ]
}
const weekdays = ref(defaultData())

const handleCopy = () => {
  weekdays.value.forEach((day, index) => {
    if (index !== 0) {
      day.start_time = weekdays.value[0].start_time
      day.end_time = weekdays.value[0].end_time
      day.is_holiday = weekdays.value[0].is_holiday
      day.breaks = [...weekdays.value[0].breaks]
    }
  })
}

const branchSelect = (e) => {
  const branchId = branch_id.value

  getRequest({ url: LISTING_URL, id: { branch_id: branchId } }).then((res) => {
    if (res.status) {
      if (res.data != '') {
        weekdays.value = res.data
      } else {
        weekdays.value = defaultData()
      }
    }
  })
}

const breaks = ref([])

const addInputField = (day) => {
  day.breaks.push({ start_break: moment().set({ hour: 0, minute: 0, second: 0 }).format('HH:mm'), end_break: moment().set({ hour: 0, minute: 0, second: 0 }).format('HH:mm') })
}

const deleteInputField = (day, index) => {
  day.breaks.splice(index, 1)
}

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.weekdays = weekdays.value
  values.branch_id = branch_id.value

  storeRequest({ url: STORE_URL, body: values }).then((res) => {
    if (res.status) {
      weekdays.value = res.data
      display_submit_message(res)
    }
  })
})
</script>
<style>
.multiselect-clear {
  display: none !important;
}
.clickable-text {
  display: inline-block;
  cursor: pointer;
}
.bussiness-hour .data-scrollbar {
  height: 700px;
  overflow-y: auto;
}
.background_colour {
  background-color: #50494917 !important;
  cursor: not-allowed;
}
.copy-icon {
  color: gray;
}
</style>
