// import "./calender"
import { InitApp } from '@/helpers/main'
import { createPinia } from 'pinia'

import QuickBooking from './quick-booking/QuickBooking.vue'

import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const pinia = createPinia()
const app = InitApp()

app.component('quickBooking', QuickBooking)

app.use(pinia)
app.use(VueTelInput)
app.mount('[data-render="app"]');