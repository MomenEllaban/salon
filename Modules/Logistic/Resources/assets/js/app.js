import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import LogisticZoneOffcanvas from './components/LogisticZoneOffcanvas.vue'

import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'
const app = InitApp()

app.use(VueTelInput)
app.component('form-offcanvas', FormOffcanvas)
app.component('logistic-zone-offcanvas', LogisticZoneOffcanvas)

app.mount('[data-render="app"]');
