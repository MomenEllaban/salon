<template>
  <form @submit="formSubmit">
    <div class="col-md-12 d-flex justify-content-between">
      <div class="d-flex">
        <CardTitle :title="$t('setting_sidebar.lbl_holiday')" icon="fa-solid fa-calendar-check"></CardTitle>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-12">
        <div class="form-group" v-if="branch.options.length > 1">
          <label class="form-label">{{ $t('branch.select_branch') }}<span class="text-danger">*</span> </label>
          <Multiselect id="branch_id" v-model="branch_id" placeholder="Select Branch" v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" class="form-group"></Multiselect>
          <span class="text-danger">{{ errors.branch_id }}</span>
        </div>
      </div>
      <div class="col-12">
        <FullCalendar :options="calendarOptions" />
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'

import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import multiMonthPlugin from '@fullcalendar/multimonth'
import { LISTING_URL, STORE_URL } from '@/vue/constants/holiday'
import { BRANCH_LIST } from '@/vue/constants/branch'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useField, useForm } from 'vee-validate'
import { useSelect } from '@/helpers/hooks/useSelect'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import * as yup from 'yup'
import moment from 'moment'
import SubmitButton from './Forms/SubmitButton.vue'

const events = ref([])
const IS_SUBMITED = ref(false)

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const handleDateClick = (e) => {
  const checkEventsList = events.value.find((event) => event.date == moment(e.date).format('YYYY-MM-DD'))
  if (typeof checkEventsList !== typeof undefined) {
    events.value.splice(
      events.value.findIndex((event) => event.date == moment(e.date).format('YYYY-MM-DD')),
      1
    )
  } else {
    events.value.push({ title: 'Holiday', date: moment(e.date).format('YYYY-MM-DD') })
  }
}

const { getRequest, storeRequest, listingRequest } = useRequest()

const validationSchema = yup.object({
  branch_id: yup.string().required('Branch is required'),
})
const { handleSubmit, errors } = useForm({ validationSchema })

const { value: branch_id } = useField('branch_id')

const branch = ref({ options: [], list: [] })
onMounted(() => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
    if (data.options.length === 1) {
      branch_id.value = data.options[0].value
    }
  })
})

const branchSelect = (e) => {
  Object.entries(events.value).forEach(([key, value]) => {
    if (value.date === moment(value.date).format('YYYY-MM-DD')) {
      events.value.splice(parseInt(key))
    }
  })
  fetchData()
}

const fetchData = () => {
  getRequest({ url: LISTING_URL, id: { branch_id: branch_id.value } }).then((res) => {
    if (res.status) {
      Object.entries(res.data).forEach(([key, value]) => {
        events.value.push({ title: value.title, date: moment(value.date).format('YYYY-MM-DD') })
      })
    }
  })
}

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, multiMonthPlugin],
  headerToolbar: {
    left: 'prev',
    center: 'title',
    right: 'next'
  },
  dateClick: handleDateClick,
  initialView: 'multiMonthYear',
  events: events.value
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.holidays = events.value
  storeRequest({ url: STORE_URL, body: values }).then((res) => display_submit_message(res))
})
</script>
<style>
.fc-h-event {
  --fc-event-bg-color: var(--bs-primary);
  --fc-event-border-color: var(--bs-primary);
}

.dark{
  .fc-daygrid-day-frame{
    background-color: var(--bs-black);   
  }
}
</style>
